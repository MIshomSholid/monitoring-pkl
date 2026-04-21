@extends('dashboard.siswa-dashboard.layout')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Riwayat Laporan Kegiatan PKL
        </h1>
        <p class="text-sm text-gray-600 mt-1">
            Daftar laporan kegiatan harian yang telah Anda kirim
        </p>
    </div>

    {{-- ================= DESKTOP TABLE ================= --}}
    <div class="hidden lg:block bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Deskripsi Kegiatan</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Bukti</th>
                    <th class="px-4 py-3 text-left">Catatan Guru</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($laporan as $item)
                    <tr class="border-b align-top hover:bg-gray-50">

                        <td class="px-4 py-3">
                            {{ $item->tanggal->format('d M Y') }}
                        </td>

                        <td class="px-4 py-3">
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

                        <td class="px-4 py-3">
                            @if ($item->file_bukti)
                                <a href="{{ asset('storage/' . $item->file_bukti) }}"
                                target="_blank"
                                class="text-blue-600 hover:underline">
                                    Lihat File
                                </a>
                            @else
                                -
                            @endif
                        </td>

                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $item->catatan_validasi ?? '-' }}
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Belum ada laporan kegiatan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $laporan->links() }}
        </div>
    </div>



    {{-- ================= MOBILE CARD ================= --}}
    <div class="lg:hidden space-y-4">
        @forelse ($laporan as $item)

            <div class="bg-white rounded-xl shadow p-4 text-sm space-y-3">

                <div class="flex justify-between items-start">
                    <div class="font-semibold text-gray-800">
                        {{ $item->tanggal->format('d M Y') }}
                    </div>

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

                <div class="text-gray-700">
                    {{ $item->deskripsi_kegiatan }}
                </div>

                <div>
                    @if ($item->file_bukti)
                        <a href="{{ asset('storage/' . $item->file_bukti) }}"
                        target="_blank"
                        class="text-blue-600 hover:underline">
                            Lihat File
                        </a>
                    @else
                        <span class="text-gray-400">Tidak ada file</span>
                    @endif
                </div>

                <div class="text-sm text-gray-600">
                    <span class="font-medium">Catatan Guru:</span><br>
                    {{ $item->catatan_validasi ?? '-' }}
                </div>

            </div>

        @empty
            <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
                Belum ada laporan kegiatan
            </div>
        @endforelse
    </div>

</div>
@endsection
