@extends('dashboard.admin.layout')

@section('content')
    <div class="p-6 bg-gray-100 min-h-screen">

        {{-- HEADER --}}
        <div class="mb-8 border-b pb-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                Dashboard Administrator
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Sistem Monitoring dan Manajemen Praktik Kerja Lapangan
            </p>
            <p class="text-sm text-gray-600">
                Pengguna: {{ auth()->user()->username }}
            </p>
        </div>

        {{-- ================= GRAFIK ================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">

            {{-- Grafik Tren Presensi --}}
            <div class="bg-white border p-6">
                <h3 class="text-md font-semibold text-gray-700 mb-4">
                    Presensi 6 Bulan Terakhir
                </h3>
                <canvas id="chartPresensi"></canvas>
            </div>

            {{-- Grafik Status Validasi --}}
            <div class="bg-white border p-6">
                <h3 class="text-md font-semibold text-gray-700 mb-4">
                    Distribusi Status Validasi
                </h3>
                <canvas id="chartValidasi"></canvas>
            </div>

            {{-- Grafik Komposisi Presensi --}}
            <div class="bg-white border p-6">
                <h3 class="text-md font-semibold text-gray-700 mb-4">
                    Komposisi Presensi
                </h3>
                <div class="relative h-48">
                    <canvas id="chartKomposisi"></canvas>
                </div>
            </div>

        </div>

        {{-- ================= STATISTIK UMUM ================= --}}
        <div class="mb-10">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Ringkasan Data Umum
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Total Siswa PKL</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-2">
                        {{ $totalSiswa }}
                    </p>
                </div>

                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Total Tempat PKL</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-2">
                        {{ $totalTempat }}
                    </p>
                </div>

                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Periode Aktif</p>
                    <p class="text-lg font-semibold text-gray-800 mt-2">
                        @if($periodeAktif)
                            {{ optional($periodeAktif)->tahun_ajaran ?? '-' }}
                        @else
                            Tidak Ada Periode Aktif
                        @endif
                    </p>
                </div>

                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Penempatan Aktif</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-2">
                        {{ $penempatanAktif }}
                    </p>
                </div>

            </div>
        </div>


        {{-- ================= MONITORING HARI INI ================= --}}
        <div class="mb-10">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Monitoring Hari Ini
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Total Presensi --}}
                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Jumlah Presensi Hari Ini</p>
                    <p class="text-xl font-semibold text-gray-800 mt-2">
                        {{ $presensiHariIni }}
                    </p>
                </div>

                {{-- Total Laporan --}}
                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Jumlah Laporan Masuk Hari Ini</p>
                    <p class="text-xl font-semibold text-gray-800 mt-2">
                        {{ $laporanHariIni }}
                    </p>
                </div>

                {{-- Hadir --}}
                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Presensi Hadir Hari Ini</p>
                    <p class="text-xl font-semibold text-green-600 mt-2">
                        {{ $hadirHariIni }}
                    </p>
                </div>

                {{-- Izin --}}
                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Presensi Izin Hari Ini</p>
                    <p class="text-xl font-semibold text-yellow-600 mt-2">
                        {{ $izinHariIni }}
                    </p>
                </div>

                {{-- Alpha --}}
                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Presensi Alpha Hari Ini</p>
                    <p class="text-xl font-semibold text-red-600 mt-2">
                        {{ $alphaHariIni }}
                    </p>
                </div>

                {{-- Libur --}}
                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Presensi Libur Hari Ini</p>
                    <p class="text-xl font-semibold text-blue-600 mt-2">
                        {{ $liburHariIni }}
                    </p>
                </div>

            </div>
        </div>

        {{-- ================= STATUS VALIDASI ================= --}}
        <div class="mb-10">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Status Validasi Data
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Presensi Menunggu</p>
                    <p class="text-xl font-semibold text-gray-800 mt-2">
                        {{ $presensiPending }}
                    </p>
                </div>

                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Laporan Harian Menunggu</p>
                    <p class="text-xl font-semibold text-gray-800 mt-2">
                        {{ $laporanPending }}
                    </p>
                </div>

                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Penilaian KPI Menunggu</p>
                    <p class="text-xl font-semibold text-gray-800 mt-2">
                        {{ $kpiPending }}
                    </p>
                </div>

                <div class="bg-white border p-5">
                    <p class="text-sm text-gray-500">Laporan Akhir Menunggu</p>
                    <p class="text-xl font-semibold text-gray-800 mt-2">
                        {{ $laporanAkhirPending }}
                    </p>
                </div>

            </div>
        </div>

    </div>


    {{-- ================= CHART JS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxPresensi = document.getElementById('chartPresensi');
        new Chart(ctxPresensi, {
            type: 'line',
            data: {
                labels: {!! json_encode($bulanLabels) !!},
                datasets: [
                    {
                        label: 'Hadir',
                        data: {!! json_encode($hadirData) !!},
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22,163,74,0.1)',
                        tension: 0.3
                    },
                    {
                        label: 'Izin',
                        data: {!! json_encode($izinData) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.1)',
                        tension: 0.3
                    },
                    {
                        label: 'Alpha',
                        data: {!! json_encode($alphaData) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,0.1)',
                        tension: 0.3
                    },
                    {
                        label: 'Libur',
                        data: {!! json_encode($liburData) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        const ctxValidasi = document.getElementById('chartValidasi');
        new Chart(ctxValidasi, {
            type: 'bar',
            data: {
                labels: ['Menunggu', 'Diterima', 'Ditolak'],
                datasets: [{
                    label: 'Jumlah Data',
                    data: {!! json_encode($validasiData) !!},
                    backgroundColor: ['#9ca3af', '#3b82f6', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });

        const ctxKomposisi = document.getElementById('chartKomposisi');
        new Chart(ctxKomposisi, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin', 'Alpha', 'Libur'],
                datasets: [{
                    data: [
                                    {{ $totalHadir }},
                                    {{ $totalIzin }},
                                    {{ $totalAlpha }},
                        {{ $totalLibur }}
                    ],
                    backgroundColor: [
                        '#16a34a',
                        '#f59e0b',
                        '#ef4444',
                        '#3b82f6'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

@endsection