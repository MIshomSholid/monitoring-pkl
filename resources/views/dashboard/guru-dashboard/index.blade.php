@extends('dashboard.guru-dashboard.layout')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">
    Dashboard
</h1>

{{-- ================= RINGKASAN ================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white p-5 rounded-md shadow">
        <p class="text-sm text-gray-500">Jumlah Siswa Bimbingan</p>
        <p class="mt-2 text-lg font-semibold text-indigo-600">
            {{ $jumlahSiswa }}
        </p>
    </div>

    <div class="bg-white p-5 rounded-md shadow">
        <p class="text-sm text-gray-500">Presensi Hari Ini</p>
        <p class="mt-2 text-lg font-semibold text-blue-600">
            {{ $presensiHariIni }}
        </p>
    </div>

    <div class="bg-white p-5 rounded-md shadow">
        <p class="text-sm text-gray-500">Laporan Menunggu Validasi</p>
        <p class="mt-2 text-lg font-semibold text-yellow-600">
            {{ $laporanMenunggu }}
        </p>
    </div>

    <div class="bg-white p-5 rounded-md shadow">
        <p class="text-sm text-gray-500">KPI Menunggu Validasi</p>
        <p class="mt-2 text-lg font-semibold text-green-600">
            {{ $kpiMenunggu }}
        </p>
    </div>

</div>

{{-- ================= INFORMASI AKADEMIK ================= --}}
<div class="bg-white p-6 rounded-md shadow">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Informasi Monitoring PKL
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">

        <p>
            <span class="font-medium">Peran:</span>
            Guru Pembimbing
        </p>

        <p>
            <span class="font-medium">Akses:</span>
            Monitoring & Validasi Akademik
        </p>

        <p>
            <span class="font-medium">Fokus:</span>
            Presensi, Laporan Harian, dan KPI Siswa
        </p>

        <p>
            <span class="font-medium">Catatan:</span>
            Data ditampilkan sesuai siswa bimbingan yang aktif
        </p>

    </div>
</div>

@endsection