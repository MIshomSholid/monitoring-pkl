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

        <div class="bg-white rounded-xl shadow overflow-x-auto">
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
                        <tr class="border-b hover:bg-gray-50">

                            {{-- IDENTITAS --}}
                            <td class="px-4 py-3 font-medium">
                                {{ $p->siswa->nama_lengkap }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $p->tempat->nama_perusahaan }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $p->periode->nama_periode }}
                            </td>

                            {{-- 1️⃣ REKAP PRESENSI (SEMUA PRESENSI) --}}
                            <td class="px-4 py-3 text-center">
                                @php
                                    $totalPresensi = $p->presensi->count();
                                @endphp

                                <div class="text-blue-600 font-semibold">
                                    {{ $totalPresensi }} Hari
                                </div>

                                <a href="{{ route('guru.validasi-laporan-akhir.presensi', $p->id) }}"
                                    class="text-xs text-indigo-600 hover:underline">
                                    Lihat Detail
                                </a>
                            </td>

                            {{-- 2️⃣ REKAP NILAI (TEKNIS & NON TEKNIS) --}}
                            <td class="px-4 py-3 text-center">
                                @php
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
                                @endphp

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

                            {{-- 3️⃣ REKAP LAPORAN (SEMUA LAPORAN) --}}
                            <td class="px-4 py-3 text-center">
                                @php
                                    $totalLaporan = $p->laporanKegiatan->count();
                                @endphp

                                <div class="text-gray-800 font-semibold">
                                    {{ $totalLaporan }} Kegiatan
                                </div>

                                <a href="{{ route('guru.validasi-laporan-akhir.laporan', $p->id) }}"
                                    class="text-xs text-indigo-600 hover:underline">
                                    Lihat Detail
                                </a>
                            </td>

                            {{-- STATUS VALIDASI --}}
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

                            {{-- APPROVAL --}}
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('guru.validasi-laporan-akhir.validate', $p->id) }}">
                                    @csrf

                                    @php
                                        $currentStatus = $p->laporanAkhir->status_validasi ?? 'menunggu';
                                    @endphp

                                    <select name="status_validasi"
                                        class="border rounded px-2 py-1 text-xs mb-2 w-full">

                                        <option value="menunggu"
                                            {{ $currentStatus == 'menunggu' ? 'selected' : '' }}>
                                            Menunggu
                                        </option>

                                        <option value="diterima"
                                            {{ $currentStatus == 'diterima' ? 'selected' : '' }}>
                                            Setujui
                                        </option>

                                        <option value="ditolak"
                                            {{ $currentStatus == 'ditolak' ? 'selected' : '' }}>
                                            Tolak
                                        </option>

                                    </select>

                                    <textarea name="catatan_validasi" class="border rounded px-2 py-1 text-xs w-full mb-2"
                                        rows="2" placeholder="Catatan validasi (opsional)"></textarea>

                                    <button
                                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-2 rounded">
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

    </div>
@endsection