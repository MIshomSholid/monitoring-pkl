@extends('dashboard.siswa-dashboard.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-6">

            {{-- ================= HEADER ================= --}}
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Monitoring KPI
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Ringkasan performa PKL berdasarkan penilaian pembimbing
                </p>
            </div>

            {{-- ================= HITUNG TOTAL & STATUS ================= --}}
            @php
                $rataTotal = $nilaiAkhir;

                $status = 'Kurang';
                $warna = 'text-red-500';
                $bg = 'bg-red-50';

                if ($rataTotal >= 80) {
                    $status = 'Sangat Baik';
                    $warna = 'text-green-600';
                    $bg = 'bg-green-50';
                } elseif ($rataTotal >= 70) {
                    $status = 'Baik';
                    $warna = 'text-blue-600';
                    $bg = 'bg-blue-50';
                } elseif ($rataTotal >= 60) {
                    $status = 'Cukup';
                    $warna = 'text-yellow-600';
                    $bg = 'bg-yellow-50';
                }
            @endphp

            {{-- ================= RINGKASAN TOTAL KPI ================= --}}
            <div class="bg-white border rounded-lg p-6 shadow-sm flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-1">
                        Ringkasan Nilai Total
                    </h2>
                    <p class="text-sm text-gray-600">
                        Rata-rata KPI:
                        <span class="text-2xl font-bold text-indigo-600">
                            {{ number_format($rataTotal, 2) }}
                        </span>
                    </p>
                    <p class="text-sm mt-2">
                        Status:
                        <span class="font-semibold {{ $warna }}">
                            {{ $status }}
                        </span>
                    </p>
                </div>

                <div class="px-6 py-4 rounded-lg {{ $bg }}">
                    <span class="text-3xl font-bold {{ $warna }}">
                        {{ number_format($rataTotal, 0) }}
                    </span>
                </div>
            </div>

            {{-- ================= RINGKASAN PER ASPEK ================= --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Aspek Teknis</p>
                    <p class="text-2xl font-bold">{{ number_format($nilaiTeknis, 2) }}</p>
                </div>

                <div class="bg-white border rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Aspek Non-Teknis</p>
                    <p class="text-2xl font-bold">{{ number_format($nilaiNonTeknis, 2) }}</p>
                </div>

                <div class="bg-white border rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Presensi</p>
                    <p class="text-2xl font-bold">{{ number_format($nilaiPresensi, 2) }}</p>
                </div>

                <div class="bg-white border rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Laporan</p>
                    <p class="text-2xl font-bold">{{ number_format($nilaiLaporan, 2) }}</p>
                </div>
            </div>

            {{-- ================= GRAFIK ================= --}}
            <div class="bg-white border rounded-lg p-6 shadow-sm">
                <h2 class="font-semibold text-gray-800 mb-4">
                    Grafik Ringkasan KPI
                </h2>
                <canvas id="kpiChart" height="100"></canvas>
            </div>

            {{-- ================= DETAIL KPI ================= --}}
            @if ($kpiTerbaru->count())

                <div id="detail-kpi" class="bg-white border rounded-lg p-6 shadow-sm">

                    @php
                        $first = $kpiTerbaru->first();
                    @endphp

                    {{-- HEADER TANGGAL --}}
                    <div class="border-b pb-3 mb-4">

                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 text-sm">

                            <div>
                                <p class="font-semibold text-gray-800">
                                    Tanggal Penilaian:
                                    {{ \Carbon\Carbon::parse($tanggalAktif)->translatedFormat('d F Y') }}
                                </p>

                                <p class="text-gray-600">
                                    {{ $penempatan->siswa->nama_lengkap }}
                                    : {{ $penempatan->tempatPkl->nama_perusahaan }}
                                </p>
                            </div>

                            <div class="text-gray-500">
                                Total Indikator: {{ $kpiTerbaru->count() }}
                            </div>

                        </div>

                    </div>


                    <div class="overflow-x-auto">

                        <table class="w-full text-sm border">

                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Kategori</th>
                                    <th class="px-4 py-2 text-left">Indikator</th>
                                    <th class="px-4 py-2 text-center">Nilai</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">

                                @foreach ($kpiTerbaru as $item)

                                    @php
                                        $status = $item->status_validasi ?? 'menunggu';

                                        $bgStatus = 'bg-yellow-100 text-yellow-700';
                                        $labelStatus = ucfirst($status);

                                        if ($status == 'diterima') {
                                            $bgStatus = 'bg-green-100 text-green-700';
                                            $labelStatus = 'Disetujui';
                                        } elseif ($status == 'ditolak') {
                                            $bgStatus = 'bg-red-100 text-red-700';
                                        }
                                    @endphp

                                    <tr class="hover:bg-gray-50">

                                        <td class="px-4 py-2">
                                            {{ $item->indikator->kategori->nama_kategori ?? '-' }}
                                        </td>

                                        <td class="px-4 py-2">
                                            {{ $item->indikator->nama_indikator }}
                                        </td>

                                        <td class="px-4 py-2 text-center font-semibold">
                                            {{ number_format($item->nilai, 2) }}
                                        </td>

                                        <td class="px-4 py-2 text-center">
                                            <span class="px-3 py-1 text-xs rounded {{ $bgStatus }}">
                                                {{ $labelStatus }}
                                            </span>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    {{-- NAVIGASI HARI --}}
                    <div class="flex justify-between items-center mt-6 text-sm">

                        @if ($prev)

                                    <a href="{{ route('siswa.kpi.index', ['tanggal' => $prev]) }}#detail-kpi"
                                    class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                        ← Hari Sebelumnya
                                    </a>

                        @else
                            <div></div>
                        @endif


                        @if ($next)

                                    <a href="{{ route('siswa.kpi.index', ['tanggal' => $next]) }}#detail-kpi"
                                    class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                        Hari Berikutnya →
                                    </a>

                        @endif

                    </div>

                </div>

            @else

                <div class="bg-white border rounded-lg p-6 text-center text-gray-500 shadow-sm">
                    Belum ada data penilaian KPI.
                </div>

            @endif
        </div>

    </div>

    {{-- ================= CHART ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('kpiChart');

        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Teknis', 'Non-Teknis', 'Presensi', 'Laporan'],
                    datasets: [{
                        label: 'Nilai KPI',
                        data: [
                                            {{ $nilaiTeknis }},
                                            {{ $nilaiNonTeknis }},
                                            {{ $nilaiPresensi }},
                            {{ $nilaiLaporan }}
                        ],
                        backgroundColor: [
                            '#6366F1',
                            '#22C55E',
                            '#F59E0B',
                            '#0EA5E9'
                        ],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    </script>
@endsection