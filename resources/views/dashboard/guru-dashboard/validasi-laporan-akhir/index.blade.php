@extends('dashboard.guru-dashboard.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Validasi Laporan Akhir PKL
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Validasi sebelum Admin dapat mencetak Rekap Presensi, Nilai, dan Laporan Akhir.
            </p>
        </div>

        {{-- NOTIF --}}
        @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- ================= DESKTOP TABLE ================= --}}
        <div class="hidden xl:block bg-white rounded-xl shadow overflow-x-auto">

            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-gray-50 text-gray-700 border-b">
                    <tr>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">Perusahaan</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3 text-center">Rekap Presensi</th>
                        <th class="px-4 py-3 text-center">Rekap Nilai</th>
                        <th class="px-4 py-3 text-center">Rekap Laporan</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Validasi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($penempatan as $p)

                        @php
                            $totalPresensi = $p->presensi->count();

                            $kpi = $p->penilaianKpi->where('status_validasi', 'diterima');

                            $teknis = $kpi->filter(
                                fn($i) =>
                                strtolower($i->indikator->kategori->nama_kategori) == 'aspek teknis'
                            );

                            $nonTeknis = $kpi->filter(
                                fn($i) =>
                                strtolower($i->indikator->kategori->nama_kategori) == 'aspek non-teknis'
                            );

                            $rataTeknis = $teknis->count() ? round($teknis->avg('nilai'), 2) : 0;
                            $rataNonTeknis = $nonTeknis->count() ? round($nonTeknis->avg('nilai'), 2) : 0;

                            $totalLaporan = $p->laporanKegiatan->count();

                            $currentStatus = $p->laporanAkhir->status_validasi ?? 'menunggu';
                        @endphp

                        <tr class="border-b hover:bg-gray-50">

                            <td class="px-4 py-3 font-medium">
                                {{ $p->siswa->nama_lengkap }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $p->tempat->nama_perusahaan }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $p->periode->nama_periode }}
                            </td>

                            <td class="px-4 py-3 text-center">

                                <div class="text-blue-600 font-semibold">
                                    {{ $totalPresensi }} Hari
                                </div>

                                <a href="{{ route('guru.validasi-laporan-akhir.presensi', $p->id) }}"
                                    class="text-xs text-indigo-600 hover:underline">
                                    Lihat Detail
                                </a>

                            </td>

                            <td class="px-4 py-3 text-center">

                                <div class="text-green-600 font-semibold text-xs">
                                    Teknis: {{ $rataTeknis }}
                                </div>

                                <div class="text-indigo-600 font-semibold text-xs">
                                    Non-Teknis: {{ $rataNonTeknis }}
                                </div>

                                <a href="{{ route('guru.validasi-laporan-akhir.nilai', $p->id) }}"
                                    class="text-xs text-indigo-600 hover:underline block mt-1">
                                    Lihat Detail
                                </a>

                            </td>

                            <td class="px-4 py-3 text-center">

                                <div class="text-gray-800 font-semibold">
                                    {{ $totalLaporan }} Kegiatan
                                </div>

                                <a href="{{ route('guru.validasi-laporan-akhir.laporan', $p->id) }}"
                                    class="text-xs text-indigo-600 hover:underline">
                                    Lihat Detail
                                </a>

                            </td>

                            <td class="px-4 py-3 text-center">

                                @if($p->laporanAkhir)

                                    @if($p->laporanAkhir->status_validasi == 'menunggu')
                                        <span class="px-3 py-1 text-xs rounded bg-yellow-100 text-yellow-700">
                                            Menunggu
                                        </span>

                                    @elseif($p->laporanAkhir->status_validasi == 'diterima')
                                        <span class="px-3 py-1 text-xs rounded bg-green-100 text-green-700">
                                            Disetujui
                                        </span>

                                    @else
                                        <span class="px-3 py-1 text-xs rounded bg-red-100 text-red-700">
                                            Ditolak
                                        </span>
                                    @endif

                                @else

                                    <span class="px-3 py-1 text-xs rounded bg-gray-200 text-gray-600">
                                        Belum Divalidasi
                                    </span>

                                @endif

                            </td>

                            <td class="px-4 py-3 w-64">

                                <form method="POST" action="{{ route('guru.validasi-laporan-akhir.validate', $p->id) }}">

                                    @csrf

                                    <select name="status_validasi" class="border rounded px-2 py-2 text-xs mb-2 w-full">

                                        <option value="menunggu" {{ $currentStatus == 'menunggu' ? 'selected' : '' }}>
                                            Menunggu
                                        </option>

                                        <option value="diterima" {{ $currentStatus == 'diterima' ? 'selected' : '' }}>
                                            Setujui
                                        </option>

                                        <option value="ditolak" {{ $currentStatus == 'ditolak' ? 'selected' : '' }}>
                                            Tolak
                                        </option>

                                    </select>

                                    <textarea name="catatan_validasi" class="border rounded px-2 py-2 text-xs w-full mb-2"
                                        rows="2" placeholder="Catatan validasi"></textarea>

                                    <button
                                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-2 rounded-lg transition">
                                        Simpan
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center px-4 py-6 text-gray-500">
                                Tidak ada data siswa bimbingan.
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>

        </div>

        {{-- ================= MOBILE CARD ================= --}}
        <div class="xl:hidden space-y-5">

            @forelse($penempatan as $p)

                @php
                    $totalPresensi = $p->presensi->count();

                    $kpi = $p->penilaianKpi->where('status_validasi', 'diterima');

                    $teknis = $kpi->filter(
                        fn($i) =>
                        strtolower($i->indikator->kategori->nama_kategori) == 'aspek teknis'
                    );

                    $nonTeknis = $kpi->filter(
                        fn($i) =>
                        strtolower($i->indikator->kategori->nama_kategori) == 'aspek non-teknis'
                    );

                    $rataTeknis = $teknis->count() ? round($teknis->avg('nilai'), 2) : 0;
                    $rataNonTeknis = $nonTeknis->count() ? round($nonTeknis->avg('nilai'), 2) : 0;

                    $totalLaporan = $p->laporanKegiatan->count();

                    $currentStatus = $p->laporanAkhir->status_validasi ?? 'menunggu';
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border p-4">

                    {{-- HEADER --}}
                    <div class="flex items-start justify-between gap-3">

                        <div>
                            <h2 class="font-semibold text-gray-800">
                                {{ $p->siswa->nama_lengkap }}
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $p->tempat->nama_perusahaan }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                {{ $p->periode->nama_periode }}
                            </p>
                        </div>

                        <div>

                            @if($p->laporanAkhir)

                                @if($p->laporanAkhir->status_validasi == 'menunggu')
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                        Menunggu
                                    </span>

                                @elseif($p->laporanAkhir->status_validasi == 'diterima')
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                        Disetujui
                                    </span>

                                @else
                                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                        Ditolak
                                    </span>
                                @endif

                            @endif

                        </div>

                    </div>

                    {{-- REKAP --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-5">

                        <div class="bg-blue-50 rounded-xl p-3">
                            <div class="text-xs text-gray-500 mb-1">
                                Presensi
                            </div>

                            <div class="font-semibold text-blue-700">
                                {{ $totalPresensi }} Hari
                            </div>

                            <a href="{{ route('guru.validasi-laporan-akhir.presensi', $p->id) }}"
                                class="text-xs text-indigo-600 hover:underline">
                                Lihat Detail
                            </a>
                        </div>

                        <div class="bg-green-50 rounded-xl p-3">
                            <div class="text-xs text-gray-500 mb-1">
                                Nilai KPI
                            </div>

                            <div class="text-xs text-green-700 font-semibold">
                                Teknis: {{ $rataTeknis }}
                            </div>

                            <div class="text-xs text-indigo-700 font-semibold">
                                Non-Teknis: {{ $rataNonTeknis }}
                            </div>

                            <a href="{{ route('guru.validasi-laporan-akhir.nilai', $p->id) }}"
                                class="text-xs text-indigo-600 hover:underline block mt-1">
                                Lihat Detail
                            </a>
                        </div>

                        <div class="bg-gray-100 rounded-xl p-3">
                            <div class="text-xs text-gray-500 mb-1">
                                Laporan
                            </div>

                            <div class="font-semibold text-gray-700">
                                {{ $totalLaporan }} Kegiatan
                            </div>

                            <a href="{{ route('guru.validasi-laporan-akhir.laporan', $p->id) }}"
                                class="text-xs text-indigo-600 hover:underline">
                                Lihat Detail
                            </a>
                        </div>

                    </div>

                    {{-- VALIDASI --}}
                    <form method="POST" action="{{ route('guru.validasi-laporan-akhir.validate', $p->id) }}" class="mt-5">

                        @csrf

                        <select name="status_validasi" class="border rounded-lg px-3 py-2 text-sm mb-3 w-full">

                            <option value="menunggu" {{ $currentStatus == 'menunggu' ? 'selected' : '' }}>
                                Menunggu
                            </option>

                            <option value="diterima" {{ $currentStatus == 'diterima' ? 'selected' : '' }}>
                                Setujui
                            </option>

                            <option value="ditolak" {{ $currentStatus == 'ditolak' ? 'selected' : '' }}>
                                Tolak
                            </option>

                        </select>

                        <textarea name="catatan_validasi" class="border rounded-lg px-3 py-2 text-sm w-full mb-3" rows="3"
                            placeholder="Catatan validasi (opsional)"></textarea>

                        <button
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2.5 rounded-xl transition">
                            Simpan Validasi
                        </button>

                    </form>

                </div>

            @empty

                <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
                    Tidak ada data siswa bimbingan.
                </div>

            @endforelse

        </div>

    </div>
@endsection