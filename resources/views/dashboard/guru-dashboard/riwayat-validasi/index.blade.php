@extends('dashboard.guru-dashboard.layout')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex flex-col gap-6">

        {{-- ================= HEADER ================= --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Riwayat Validasi Laporan
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Riwayat laporan kegiatan harian siswa yang telah divalidasi
            </p>
        </div>

        {{-- ================= FILTER ================= --}}
        <div class="bg-white border rounded-lg p-4 shadow-sm">
            <form method="GET"
                  class="flex flex-col sm:flex-row flex-wrap items-start sm:items-end gap-4 text-sm">

                <div class="w-full sm:w-72">
                    <label class="block mb-1 font-medium text-gray-700">
                        Nama Siswa
                    </label>
                    <select name="siswa_id"
                            class="w-full border rounded px-3 py-2 h-10">
                        <option value="">Semua Siswa</option>
                        @foreach ($daftarSiswa as $siswa)
                            <option value="{{ $siswa->id }}"
                                {{ request('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                {{ $siswa->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-56">
                    <label class="block mb-1 font-medium text-gray-700">
                        Tanggal
                    </label>
                    <input type="date"
                           name="tanggal"
                           value="{{ request('tanggal') }}"
                           class="w-full border rounded px-3 py-2 h-10">
                </div>

                <div class="w-full sm:w-48">
                    <label class="block mb-1 font-medium text-gray-700">
                        Status
                    </label>
                    <select name="status"
                            class="w-full border rounded px-3 py-2 h-10">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>
                            Menunggu
                        </option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>
                            Diterima
                        </option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                            Ditolak
                        </option>
                    </select>
                </div>

                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="submit"
                        class="h-10 px-5 bg-indigo-600 text-white rounded hover:bg-indigo-700 w-full sm:w-auto">
                        Filter
                    </button>

                    <a href="{{ route('guru.riwayat-validasi.index') }}"
                        class="h-10 px-5 bg-gray-100 rounded hover:bg-gray-200 flex items-center justify-center w-full sm:w-auto">
                        Reset
                    </a>
                </div>

            </form>
        </div>

        {{-- ================= DESKTOP TABLE ================= --}}
            <div class="hidden md:block bg-white border rounded-lg shadow-sm">
                <table class="w-full text-sm table-fixed">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Siswa</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Deskripsi</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Bukti</th>
                            <th class="px-4 py-3 text-left">Catatan Validasi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($laporan as $item)
                        <tr class="border-b hover:bg-gray-50 align-top">

                            <td class="px-4 py-3 font-medium break-words">
                                {{ $item->penempatanPkl->siswa->nama_lengkap }}
                            </td>

                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 break-words">
                                {{ $item->deskripsi_kegiatan }}
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

                            <td class="px-4 py-3 break-words">
                                @if ($item->file_bukti)
                                    <a href="{{ $item->file_bukti }}"
                                    target="_blank"
                                    class="text-indigo-600 hover:underline font-medium">
                                        Lihat File
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-600 break-words">
                                {{ $item->catatan_validasi ?? '-' }}
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                Belum ada riwayat validasi laporan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            {{-- ================= MOBILE CARD ================= --}}
            <div class="md:hidden space-y-4">
                @forelse ($laporan as $item)

                <div class="bg-white border rounded-lg shadow-sm p-4 text-sm space-y-2">

                    <div class="font-semibold text-gray-800 break-words">
                        {{ $item->penempatanPkl->siswa->nama_lengkap }}
                    </div>

                    <div class="text-gray-600">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                    </div>

                    <div class="break-words text-gray-700">
                        {{ $item->deskripsi_kegiatan }}
                    </div>

                    <div>
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
                    </div>

                    <div>
                        @if ($item->file_bukti)
                            <a href="{{ $item->file_bukti }}"
                            target="_blank"
                            class="text-indigo-600 hover:underline font-medium">
                                Lihat File
                            </a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </div>

                    <div class="text-gray-600 break-words">
                        {{ $item->catatan_validasi ?? '-' }}
                    </div>

                </div>

                @empty
                <div class="bg-white border rounded-lg shadow-sm p-6 text-center text-gray-500">
                    Belum ada riwayat validasi laporan
                </div>
                @endforelse
            </div>

        </div>

    </div>
@endsection
