@extends('dashboard.admin.layout')

@section('content')
<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
            Rekap Laporan Kegiatan PKL
        </h1>
        <p class="text-sm text-gray-600 mt-1">
            Daftar laporan kegiatan harian siswa selama pelaksanaan PKL
        </p>
    </div>


    {{-- ================= FILTER ================= --}}
    <form method="GET"
          action="{{ route('admin.rekap-laporan') }}"
          class="bg-white rounded-xl shadow p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nama Siswa
            </label>
            <input type="text"
                   name="nama_siswa"
                   value="{{ request('nama_siswa') }}"
                   placeholder="Cari siswa..."
                   class="w-full mt-1 rounded-md border-gray-300 text-sm focus:ring focus:ring-indigo-200"
            >
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tempat PKL
            </label>
            <select name="tempat_pkl_id" class="w-full rounded-md border-gray-300 text-sm">
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
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Periode PKL
            </label>
            <select name="periode_pkl_id" class="w-full rounded-md border-gray-300 text-sm">
                <option value="">Semua Periode</option>
                @foreach ($daftarPeriode as $periode)
                    <option value="{{ $periode->id }}"
                        {{ request('periode_pkl_id') == $periode->id ? 'selected' : '' }}>
                        {{ $periode->nama_periode }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Status Validasi
            </label>
            <select name="status_validasi" class="w-full rounded-md border-gray-300 text-sm">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ request('status_validasi') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="diterima" {{ request('status_validasi') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak"  {{ request('status_validasi') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                Filter
            </button>

            <a href="{{ route('admin.rekap-laporan') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">
                Reset
            </a>
        </div>
    </form>



    {{-- ================= DESKTOP TABLE ================= --}}
    <div class="hidden md:block bg-white rounded-xl shadow">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Nama Siswa</th>
                    <th class="px-4 py-3 text-left">Tempat PKL</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Bukti</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($laporan as $item)
                <tr class="border-b hover:bg-gray-50 align-top">
                    <td class="px-4 py-3">
                        {{ $item->tanggal->format('d M Y') }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $item->penempatanPkl->siswa->nama_lengkap ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $item->penempatanPkl->tempatPkl->nama_perusahaan ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ \Illuminate\Support\Str::limit($item->deskripsi_kegiatan, 80) }}
                    </td>

                    <td class="px-4 py-3">
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
                    </td>

                    <td class="px-4 py-3">
                        @if ($item->file_bukti)
                            <a href="{{ $item->file_bukti }}"
                               target="_blank"
                               class="text-blue-600 hover:underline text-sm">
                                Lihat File
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        Belum ada data laporan kegiatan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t">
            {{ $laporan->withQueryString()->links() }}
        </div>
    </div>



    {{-- ================= MOBILE CARD ================= --}}
    <div class="space-y-4 md:hidden">
        @forelse ($laporan as $item)

        <div class="bg-white rounded-xl shadow p-4 space-y-3">

            <div class="flex justify-between">
                <span class="text-xs text-gray-500">Tanggal</span>
                <span class="font-medium text-sm">
                    {{ $item->tanggal->format('d M Y') }}
                </span>
            </div>

            <div>
                <p class="text-xs text-gray-500">Siswa</p>
                <p class="font-medium">
                    {{ $item->penempatanPkl->siswa->nama_lengkap ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-500">Tempat PKL</p>
                <p>
                    {{ $item->penempatanPkl->tempatPkl->nama_perusahaan ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-500">Deskripsi</p>
                <p class="text-sm">
                    {{ $item->deskripsi_kegiatan }}
                </p>
            </div>

            <div class="flex justify-between items-center">
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

                @if ($item->file_bukti)
                    <a href="{{ $item->file_bukti }}"
                       target="_blank"
                       class="text-blue-600 hover:underline text-sm">
                        Lihat File
                    </a>
                @endif
            </div>

        </div>

        @empty
        <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
            Belum ada data laporan kegiatan
        </div>
        @endforelse

        <div>
            {{ $laporan->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection
