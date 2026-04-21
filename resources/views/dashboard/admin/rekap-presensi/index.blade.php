@extends('dashboard.admin.layout')

@section('content')
<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
            Rekap Presensi PKL
        </h1>
        <p class="text-sm text-gray-600 mt-1">
            Daftar rekap kehadiran dan izin siswa selama pelaksanaan PKL
        </p>
    </div>

    {{-- FILTER --}}
    <form method="GET"
          action="{{ route('admin.rekap-presensi') }}"
          class="bg-white rounded-xl shadow p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div>
            <label class="text-sm font-medium text-gray-700">Nama Siswa</label>
            <input
                type="text"
                name="nama_siswa"
                value="{{ request('nama_siswa') }}"
                placeholder="Cari siswa..."
                class="w-full mt-1 rounded-md border-gray-300 text-sm focus:ring focus:ring-indigo-200"
            >
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Tempat PKL</label>
            <select name="tempat_pkl_id" class="w-full mt-1 rounded-md border-gray-300 text-sm">
                <option value="">Semua Tempat</option>
                @foreach ($daftarTempatPkl as $tempat)
                    <option value="{{ $tempat->id }}"
                        {{ request('tempat_pkl_id') == $tempat->id ? 'selected' : '' }}>
                        {{ $tempat->nama_perusahaan }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Periode PKL</label>
            <select name="periode_pkl_id" class="w-full mt-1 rounded-md border-gray-300 text-sm">
                <option value="">Semua Periode</option>
                @foreach ($daftarPeriode as $periode)
                    <option value="{{ $periode->id }}"
                        {{ request('periode_pkl_id') == $periode->id ? 'selected' : '' }}>
                        {{ $periode->nama_periode }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">
                Filter
            </button>
            <a href="{{ route('admin.rekap-presensi') }}"
               class="px-4 py-2 bg-gray-200 rounded-md text-sm">
                Reset
            </a>
        </div>
    </form>


    {{-- ================= DESKTOP TABLE ================= --}}
    <div class="hidden md:block bg-white rounded-xl shadow">
        <table class="w-full text-sm table-auto" data-realtime="rekap-presensi">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Nama Siswa</th>
                    <th class="px-4 py-3 text-left">Tempat PKL</th>
                    <th class="px-4 py-3 text-left">Jenis</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Bukti</th>
                </tr>
            </thead>

            <tbody id="presensi-table-body">
                @forelse ($presensi as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $item->penempatanPkl->siswa->nama_lengkap ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $item->penempatanPkl->tempatPkl->nama_perusahaan ?? '-' }}
                    </td>

                    <td class="px-4 py-2">
                        @php
                            $badge = 'bg-gray-100 text-gray-700';
                            $label = ucfirst($item->jenis_presensi);

                            if ($item->jenis_presensi === 'hadir') {
                                $badge = 'bg-green-100 text-green-700';
                                $label = 'Hadir';
                            } elseif ($item->jenis_presensi === 'izin') {
                                $badge = 'bg-yellow-100 text-yellow-700';
                                $label = 'Izin';
                            } elseif ($item->jenis_presensi === 'libur') {
                                $badge = 'bg-slate-200 text-slate-700';
                                $label = 'Libur';
                            } elseif ($item->jenis_presensi === 'alpha') {
                                $badge = 'bg-red-100 text-red-700';
                                $label = 'Alpha';
                            }
                        @endphp

                        <span class="px-2 py-1 text-xs rounded {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>

                    <td class="px-4 py-2">
                        @switch($item->status_validasi)
                            @case('menunggu')
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                                    Menunggu
                                </span>
                                @break

                            @case('diterima')
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                    Diterima
                                </span>
                                @break

                            @default
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                    Ditolak
                                </span>
                        @endswitch

                        @if($item->status_validasi === 'ditolak' && $item->alasan_penolakan)
                            <div class="text-xs text-red-600 mt-1">
                                Alasan: {{ $item->alasan_penolakan }}
                            </div>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        @if ($item->jenis_presensi === 'hadir' && $item->file_bukti)
                            <a href="{{ asset('storage/'.$item->file_bukti) }}"
                            target="_blank"
                            class="text-blue-600 hover:underline">
                                Lihat Foto
                            </a>

                        @elseif ($item->jenis_presensi === 'izin' && $item->file_bukti)
                            <a href="{{ asset('storage/'.$item->file_bukti) }}"
                            target="_blank"
                            class="text-blue-600 hover:underline">
                                Lihat Bukti
                            </a>

                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t">
            {{ $presensi->withQueryString()->links() }}
        </div>
    </div>


    {{-- ================= MOBILE CARD ================= --}}
    <div class="space-y-4 md:hidden">
        @forelse ($presensi as $item)

        <div class="bg-white rounded-xl shadow p-4 space-y-3">

            <div class="flex justify-between">
                <span class="text-xs text-gray-500">Tanggal</span>
                <span class="font-medium text-sm">
                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                </span>
            </div>

            <div>
                <p class="text-xs text-gray-500">Siswa</p>
                <p class="font-medium">{{ $item->penempatanPkl->siswa->nama_lengkap ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500">Tempat</p>
                <p>{{ $item->penempatanPkl->tempatPkl->nama_perusahaan ?? '-' }}</p>
            </div>

            <div class="flex gap-2 flex-wrap">
                @php
                    $badge = 'bg-gray-100 text-gray-700';
                    $label = ucfirst($item->jenis_presensi);

                    if ($item->jenis_presensi === 'hadir') {
                        $badge = 'bg-green-100 text-green-700';
                        $label = 'Hadir';
                    } elseif ($item->jenis_presensi === 'izin') {
                        $badge = 'bg-yellow-100 text-yellow-700';
                        $label = 'Izin';
                    } elseif ($item->jenis_presensi === 'libur') {
                        $badge = 'bg-slate-200 text-slate-700';
                        $label = 'Libur';
                    } elseif ($item->jenis_presensi === 'alpha') {
                        $badge = 'bg-red-100 text-red-700';
                        $label = 'Alpha';
                    }
                @endphp

                <span class="px-2 py-1 text-xs rounded {{ $badge }}">
                    {{ $label }}
                </span>

                @switch($item->status_validasi)
                    @case('menunggu')
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                            Menunggu
                        </span>
                        @break
                    @case('diterima')
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                            Diterima
                        </span>
                        @break
                    @default
                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                            Ditolak
                        </span>
                @endswitch

                @if($item->status_validasi === 'ditolak' && $item->alasan_penolakan)
                    <div class="text-xs text-red-600 mt-2">
                        Alasan: {{ $item->alasan_penolakan }}
                    </div>
                @endif
            </div>

            <div>
                <p class="text-xs text-gray-500">Bukti</p>
                @if ($item->file_bukti)
                    <a href="{{ asset('storage/'.$item->file_bukti) }}"
                       target="_blank"
                       class="text-blue-600 hover:underline text-sm">
                        Lihat File
                    </a>
                @else
                    -
                @endif
            </div>

        </div>

        @empty
        <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
            Tidak ada data
        </div>
        @endforelse

        <div>
            {{ $presensi->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection
