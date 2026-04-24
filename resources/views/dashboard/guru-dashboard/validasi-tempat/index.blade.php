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

    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3">Siswa</th>
                    <th class="px-4 py-3">Tempat</th>
                    <th class="px-4 py-3 text-center">Status Saat Ini</th>
                    <th class="px-4 py-3 text-center">Pengajuan</th>
                    <th class="px-4 py-3 text-center">Status Validasi</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($data as $item)

                    @php
                        $statusClass = $item->status == 'aktif'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700';

                        $pengajuanClass = match ($item->status_pengajuan) {
                            'aktif' => 'bg-green-100 text-green-700',
                            'nonaktif' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-500',
                        };

                        $validasiClass = match ($item->status_validasi) {
                            'diterima' => 'bg-green-100 text-green-700',
                            'ditolak' => 'bg-red-100 text-red-700',
                            default => 'bg-yellow-100 text-yellow-700',
                        };
                    @endphp

                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">
                            {{ $item->siswa->nama_lengkap }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $item->tempat->nama_perusahaan }}
                        </td>

                        {{-- STATUS SAAT INI --}}
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs rounded {{ $statusClass }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>

                        {{-- PENGAJUAN --}}
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs rounded {{ $pengajuanClass }}">
                                {{ $item->status_pengajuan ? ucfirst($item->status_pengajuan) : '-' }}
                            </span>
                        </td>

                        {{-- STATUS VALIDASI --}}
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs rounded {{ $validasiClass }}">
                                {{ ucfirst($item->status_validasi ?? 'menunggu') }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 py-3">
                            <form method="POST"
                                  action="{{ route('guru.validasi-tempat.validate', $item->id) }}">
                                @csrf

                                <select name="status_validasi"
                                    class="border rounded px-2 py-1 text-xs mb-2 w-full">

                                    <option value="menunggu">Menunggu</option>
                                    <option value="diterima">Setujui</option>
                                    <option value="ditolak">Tolak</option>

                                </select>

                                <button
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-2 rounded">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="text-center px-4 py-6 text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection