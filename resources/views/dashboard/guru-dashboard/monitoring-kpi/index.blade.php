@extends('dashboard.guru-dashboard.layout')

@section('content')

    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-6">

            {{-- ================= HEADER ================= --}}
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Monitoring KPI Siswa
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Grafik perkembangan kinerja siswa berdasarkan penilaian KPI
                </p>
            </div>

            {{-- ================= FILTER ================= --}}
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <form method="GET" class="flex flex-wrap items-end gap-4 text-sm">

                    <div class="w-full sm:w-80">
                        <label class="block mb-1 font-medium text-gray-700">
                            Siswa Bimbingan
                        </label>

                        <select name="penempatan_pkl_id"
                            class="w-full border rounded px-3 py-2 h-10 focus:ring-2 focus:ring-indigo-500"
                            onchange="this.form.submit()">

                            <option value="">Pilih Siswa</option>

                            @foreach ($penempatan as $p)
                                <option value="{{ $p->id }}" {{ $penempatanId == $p->id ? 'selected' : '' }}>
                                    {{ $p->siswa->nama_lengkap }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                </form>
            </div>

            @if ($penempatanId)

                {{-- ================= RINGKASAN NILAI ================= --}}
                @php
                    $status = 'Kurang';
                    $warna = 'text-red-500';
                    $bg = 'bg-red-50';

                    if ($rataRata >= 80) {
                        $status = 'Sangat Baik';
                        $warna = 'text-green-600';
                        $bg = 'bg-green-50';
                    } elseif ($rataRata >= 70) {
                        $status = 'Baik';
                        $warna = 'text-blue-600';
                        $bg = 'bg-blue-50';
                    } elseif ($rataRata >= 60) {
                        $status = 'Cukup';
                        $warna = 'text-yellow-600';
                        $bg = 'bg-yellow-50';
                    }
                @endphp

                <div class="bg-white border rounded-lg p-6 shadow-sm flex justify-between items-center">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-1">
                            Ringkasan Nilai
                        </h2>

                        <p class="text-sm text-gray-600">
                            Rata-rata KPI:
                            <span class="text-2xl font-bold text-indigo-600">
                                {{ number_format($rataRata ?? 0, 2) }}
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
                            {{ number_format($rataRata ?? 0, 0) }}
                        </span>
                    </div>

                </div>


                {{-- ================= RINGKASAN PER ASPEK ================= --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    @foreach ($grafik as $label => $nilai)

                        <div class="bg-white border rounded-lg p-4 shadow-sm">
                            <p class="text-sm text-gray-500">
                                {{ $label }}
                            </p>
                            <p class="text-2xl font-bold text-gray-800">
                                {{ number_format($nilai, 2) }}
                            </p>
                        </div>

                    @endforeach

                </div>

                {{-- ================= NILAI SETELAH BOBOT ================= --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- TEKNIS --}}
                    <div class="bg-indigo-50 border rounded-lg p-4 shadow-sm">
                        <p class="text-sm text-gray-500">
                            Teknis ({{ $bobotTeknis * 100 }}%)
                        </p>

                        <p class="text-2xl font-bold text-indigo-700">
                            {{ number_format($nilaiTeknisBobot, 2) }}
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($grafik['Teknis'], 2) }} × {{ $bobotTeknis }}
                        </p>
                    </div>

                    {{-- NON TEKNIS --}}
                    <div class="bg-green-50 border rounded-lg p-4 shadow-sm">
                        <p class="text-sm text-gray-500">
                            Non Teknis ({{ $bobotNonTeknis * 100 }}%)
                        </p>

                        <p class="text-2xl font-bold text-green-700">
                            {{ number_format($nilaiNonTeknisBobot, 2) }}
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($grafik['Non Teknis'], 2) }} × {{ $bobotNonTeknis }}
                        </p>
                    </div>

                    {{-- PRESENSI --}}
                    <div class="bg-yellow-50 border rounded-lg p-4 shadow-sm">
                        <p class="text-sm text-gray-500">
                            Presensi ({{ $bobotPresensi * 100 }}%)
                        </p>

                        <p class="text-2xl font-bold text-yellow-700">
                            {{ number_format($nilaiPresensiBobot, 2) }}
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($grafik['Presensi'], 2) }} × {{ $bobotPresensi }}
                        </p>
                    </div>

                    {{-- LAPORAN --}}
                    <div class="bg-blue-50 border rounded-lg p-4 shadow-sm">
                        <p class="text-sm text-gray-500">
                            Laporan ({{ $bobotLaporan * 100 }}%)
                        </p>

                        <p class="text-2xl font-bold text-blue-700">
                            {{ number_format($nilaiLaporanBobot, 2) }}
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($grafik['Laporan'], 2) }} × {{ $bobotLaporan }}
                        </p>
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
                @if ($kpi->count())

                    <div class="bg-white border rounded-lg p-6 shadow-sm">

                        @php
                            $first = $kpi->first();
                        @endphp

                        {{-- HEADER TANGGAL --}}
                        <div class="border-b pb-3 mb-4">

                            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 text-sm">

                                <div>
                                    <p class="font-semibold text-gray-800">
                                        Tanggal Penilaian:
                                        @if($tanggalAktif)
                                            {{ \Carbon\Carbon::parse($tanggalAktif)->translatedFormat('d F Y') }}
                                        @else
                                            -
                                        @endif
                                    </p>

                                    <p class="text-gray-600">
                                        {{ optional($penempatanModel->siswa)->nama_lengkap ?? '-' }}
                                        :
                                        {{ optional($penempatanModel->tempatPkl)->nama_perusahaan ?? '-' }}
                                    </p>
                                </div>

                                <div class="text-gray-500">
                                    Total Indikator: {{ $kpi->count() }}
                                </div>

                            </div>

                        </div>

                        <div class="overflow-x-auto">

                            <table class="w-full text-sm border">

                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Kategori</th>
                                        <th class="px-4 py-2 text-left">Indikator</th>
                                        <th class="px-4 py-2 text-left">Nilai</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y">

                                    @foreach ($kpi as $row)

                                        @php
                                            $status = $row->status_validasi ?? 'menunggu';

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
                                                {{ $row->indikator->kategori->nama_kategori }}
                                            </td>

                                            <td class="px-4 py-2">
                                                {{ $row->indikator->nama_indikator }}
                                            </td>

                                            <td class="px-4 py-2 font-semibold text-gray-800">
                                                {{ number_format($row->nilai, 2) }}
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

                                    <a href="{{ route('guru.monitoring-kpi.index', [
                                    'penempatan_pkl_id' => $penempatanId,
                                    'tanggal' => $prev
                                ]) }}" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                        ← Hari Sebelumnya
                                    </a>

                            @else
                                <div></div>
                            @endif


                            @if ($next)

                                    <a href="{{ route('guru.monitoring-kpi.index', [
                                    'penempatan_pkl_id' => $penempatanId,
                                    'tanggal' => $next
                                ]) }}" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                        Hari Berikutnya →
                                    </a>

                            @endif

                        </div>

                    </div>

                @else

                    <div class="bg-white border rounded-lg p-6 text-center text-gray-500 shadow-sm">
                        Tidak ada data KPI
                    </div>

                @endif


            @else

                <div class="text-center text-gray-500 py-12">
                    Silakan pilih siswa untuk melihat data KPI
                </div>

            @endif

        </div>
    </div>


    {{-- ================= CHART ================= --}}
    @if ($penempatanId && $grafik->count())

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

                            data: {!! json_encode($grafik->values()) !!},

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

    @endif

@endsection