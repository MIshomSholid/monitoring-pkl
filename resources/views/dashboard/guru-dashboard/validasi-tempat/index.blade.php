@extends('dashboard.guru-dashboard.layout')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Validasi Penempatan PKL
        </h1>
        <p class="text-sm text-gray-600 mt-1">
            Validasi pengajuan penempatan siswa sebelum digunakan dalam sistem.
        </p>
    </div>

    {{-- ================= DESKTOP TABLE ================= --}}
    <div class="hidden lg:block bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left">Siswa</th>
                    <th class="px-4 py-3 text-left">Tempat</th>
                    <th class="px-4 py-3 text-center">Status Saat Ini</th>
                    <th class="px-4 py-3 text-center">Pengajuan</th>
                    <th class="px-4 py-3 text-center">Status Validasi</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($data as $item)
                    @php
                        $statusClass = $item->status === 'aktif'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700';

                        $pengajuanClass = match ($item->status_pengajuan) {
                            'aktif'    => 'bg-green-100 text-green-700',
                            'nonaktif' => 'bg-red-100 text-red-700',
                            default    => 'bg-gray-100 text-gray-500',
                        };

                        $validasiClass = match ($item->status_validasi) {
                            'diterima' => 'bg-green-100 text-green-700',
                            'ditolak'  => 'bg-red-100 text-red-700',
                            default    => 'bg-yellow-100 text-yellow-700',
                        };

                        $current  = $item->status_validasi ?? 'menunggu';

                        // Disabled jika tidak ada pengajuan ATAU sudah diterima
                        $disabled = empty($item->status_pengajuan) || $item->status_validasi === 'diterima';
                    @endphp

                    <tr class="border-b hover:bg-gray-50 transition-colors">

                        <td class="px-4 py-3 font-medium">
                            {{ $item->siswa->nama_lengkap }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $item->tempat->nama_perusahaan }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs rounded {{ $statusClass }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs rounded {{ $pengajuanClass }}">
                                {{ $item->status_pengajuan ? ucfirst($item->status_pengajuan) : '-' }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs rounded {{ $validasiClass }}">
                                {{ ucfirst($current) }}
                            </span>
                        </td>

                        <td class="px-4 py-3 w-56">
                            <form method="POST"
                                  action="{{ route('guru.validasi-tempat.validate', $item->id) }}">
                                @csrf

                                <select name="status_validasi"
                                        class="border rounded-lg px-2 py-2 text-xs mb-2 w-full
                                               {{ $disabled ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        {{ $disabled ? 'disabled' : '' }}>
                                    <option value="diterima"
                                            {{ $current === 'diterima' ? 'selected' : '' }}>
                                        Setujui
                                    </option>
                                    <option value="ditolak"
                                            {{ $current === 'ditolak' ? 'selected' : '' }}>
                                        Tolak
                                    </option>
                                </select>

                                @if (empty($item->status_pengajuan))
                                    <button type="button" disabled
                                            class="w-full bg-gray-300 text-gray-500 text-xs px-3 py-2 rounded-lg cursor-not-allowed">
                                        Tidak Ada Pengajuan
                                    </button>
                                @elseif ($item->status_validasi === 'diterima')
                                    <button type="button" disabled
                                            class="w-full bg-gray-400 text-white text-xs px-3 py-2 rounded-lg cursor-not-allowed">
                                        Sudah Disetujui
                                    </button>
                                @else
                                    <button type="submit"
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-2 rounded-lg transition">
                                        Simpan
                                    </button>
                                @endif

                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center px-4 py-8 text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= MOBILE CARD ================= --}}
    <div class="lg:hidden space-y-5">
        @forelse($data as $item)
            @php
                $statusClass = $item->status === 'aktif'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700';

                $pengajuanClass = match ($item->status_pengajuan) {
                    'aktif'    => 'bg-green-100 text-green-700',
                    'nonaktif' => 'bg-red-100 text-red-700',
                    default    => 'bg-gray-100 text-gray-500',
                };

                $validasiClass = match ($item->status_validasi) {
                    'diterima' => 'bg-green-100 text-green-700',
                    'ditolak'  => 'bg-red-100 text-red-700',
                    default    => 'bg-yellow-100 text-yellow-700',
                };

                $current  = $item->status_validasi ?? 'menunggu';
                $disabled = empty($item->status_pengajuan) || $item->status_validasi === 'diterima';
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border p-4">

                {{-- HEADER CARD --}}
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-gray-800">
                            {{ $item->siswa->nama_lengkap }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $item->tempat->nama_perusahaan }}
                        </p>
                    </div>

                    <span class="px-3 py-1 text-xs rounded-full {{ $validasiClass }} shrink-0">
                        {{ ucfirst($current) }}
                    </span>
                </div>

                {{-- STATUS BADGES --}}
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <div class="text-xs text-gray-500 mb-1">Status Saat Ini</div>
                        <span class="px-3 py-1 text-xs rounded-full {{ $statusClass }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-3">
                        <div class="text-xs text-gray-500 mb-1">Pengajuan</div>
                        <span class="px-3 py-1 text-xs rounded-full {{ $pengajuanClass }}">
                            {{ $item->status_pengajuan ? ucfirst($item->status_pengajuan) : '-' }}
                        </span>
                    </div>
                </div>

                {{-- FORM VALIDASI --}}
                <form method="POST"
                      action="{{ route('guru.validasi-tempat.validate', $item->id) }}"
                      class="mt-4">
                    @csrf

                    <select name="status_validasi"
                            class="border rounded-xl px-3 py-2 text-sm mb-3 w-full
                                   {{ $disabled ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                            {{ $disabled ? 'disabled' : '' }}>
                        <option value="diterima"
                                {{ $current === 'diterima' ? 'selected' : '' }}>
                            Setujui
                        </option>
                        <option value="ditolak"
                                {{ $current === 'ditolak' ? 'selected' : '' }}>
                            Tolak
                        </option>
                    </select>

                    @if (empty($item->status_pengajuan))
                        <button type="button" disabled
                                class="w-full bg-gray-300 text-gray-500 text-sm px-4 py-2.5 rounded-xl cursor-not-allowed">
                            Tidak Ada Pengajuan
                        </button>
                    @elseif ($item->status_validasi === 'diterima')
                        <button type="button" disabled
                                class="w-full bg-gray-400 text-white text-sm px-4 py-2.5 rounded-xl cursor-not-allowed">
                            Sudah Disetujui
                        </button>
                    @else
                        <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2.5 rounded-xl transition">
                            Simpan Validasi
                        </button>
                    @endif

                </form>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
                Tidak ada data
            </div>
        @endforelse
    </div>

</div>
@endsection