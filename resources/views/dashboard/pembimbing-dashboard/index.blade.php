@extends('dashboard.pembimbing-dashboard.layout')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">
    Dashboard
</h1>

{{-- ================= RINGKASAN ================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

    <div class="bg-white p-5 rounded-md shadow">
        <p class="text-sm text-gray-500">Jumlah Siswa PKL</p>
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
        <p class="text-sm text-gray-500">Presensi Menunggu Validasi</p>
        <p class="mt-2 text-lg font-semibold text-yellow-600">
            {{ $presensiMenunggu }}
        </p>
    </div>

</div>

{{-- ================= INFORMASI MONITORING ================= --}}
<div class="bg-white p-6 rounded-md shadow">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Informasi Monitoring PKL
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">

        <p>
            <span class="font-medium">Peran:</span>
            Pembimbing Lapangan
        </p>

        <p>
            <span class="font-medium">Lingkungan:</span>
            Industri / Perusahaan
        </p>

        <p>
            <span class="font-medium">Tanggung Jawab:</span>
            Validasi presensi dan penilaian KPI siswa
        </p>

        <p>
            <span class="font-medium">Catatan:</span>
            Presensi divalidasi berdasarkan lokasi & bukti pendukung
        </p>

    </div>
</div>

@endsection