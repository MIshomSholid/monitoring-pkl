@extends('dashboard.siswa-dashboard.layout')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Dashboard
    </h1>

    {{-- ================= RINGKASAN ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="bg-white p-5 rounded-md shadow">
            <p class="text-sm text-gray-500">Presensi Hari Ini</p>
            <p class="mt-2 text-lg font-semibold text-indigo-600">
                {{ $statusPresensi }}
            </p>
        </div>

        <div class="bg-white p-5 rounded-md shadow">
            <p class="text-sm text-gray-500">Status Laporan Terakhir</p>
            <p class="mt-2 text-lg font-semibold text-yellow-600 capitalize">
                {{ optional($laporanTerakhir)->status_validasi ?? 'Belum Ada Laporan' }}
            </p>
        </div>

        <div class="bg-white p-5 rounded-md shadow">
            <p class="text-sm text-gray-500">Nilai KPI</p>
            <p class="mt-2 text-lg font-semibold text-green-600">
                {{ $totalKpi }} / 100
            </p>
        </div>

        <div class="bg-white p-5 rounded-md shadow">
            <p class="text-sm text-gray-500">Tempat PKL</p>
            <p class="mt-2 text-md font-medium text-gray-800">
                {{ optional($penempatan->tempatPkl)->nama_perusahaan ?? '-' }}
            </p>
        </div>

    </div>

    {{-- ================= INFORMASI PKL ================= --}}
    <div class="bg-white p-6 rounded-md shadow mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            Informasi PKL
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">

            <p>
                <span class="font-medium">Nama Siswa:</span>
                {{ $siswa->nama_lengkap }}
            </p>

            <p>
                <span class="font-medium">Periode PKL:</span>
                {{ optional($penempatan->periodePkl)->nama_periode ?? '-' }}
                @if(optional($penempatan->periodePkl)->tahun_ajaran)
                    ({{ $penempatan->periodePkl->tahun_ajaran }})
                @endif
            </p>

            <p>
                <span class="font-medium">Guru Pembimbing:</span>
                {{ optional($penempatan->guruPembimbing)->nama_lengkap ?? '-' }}
            </p>

            <p>
                <span class="font-medium">Pembimbing Lapangan:</span>
                {{ optional($penempatan->pembimbingLapangan)->nama_lengkap ?? '-' }}
            </p>

        </div>
    </div>

    {{-- ================= CATATAN EVALUASI ================= --}}
    <div class="bg-white p-6 rounded-md shadow">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            Catatan & Evaluasi Terbaru
        </h2>

        @if($catatanTerbaru)

            <p class="text-sm text-gray-700">
                {{ $catatanTerbaru->catatan }}
            </p>

            <p class="text-xs text-gray-500 mt-2">
                {{ \Carbon\Carbon::parse($catatanTerbaru->created_at)->format('d M Y, H:i') }} WIB
            </p>

        @else

            <p class="text-sm text-gray-500">
                Belum ada catatan evaluasi.
            </p>

        @endif
    </div>

@endsection