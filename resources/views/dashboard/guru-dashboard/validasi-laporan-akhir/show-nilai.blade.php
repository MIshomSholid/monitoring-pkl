@extends('dashboard.guru-dashboard.layout')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Detail Nilai KPI
        </h1>

        <a href="{{ route('guru.validasi-laporan-akhir.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">
            Kembali
        </a>
    </div>

    {{-- IDENTITAS --}}
    <div class="bg-white p-5 rounded-xl shadow mb-6">
        <p><strong>Nama Siswa:</strong> {{ $penempatan->siswa->nama_lengkap }}</p>
        <p><strong>Tempat PKL:</strong> {{ $penempatan->tempat->nama_perusahaan }}</p>
        <p><strong>Periode:</strong> {{ $penempatan->periode->nama_periode }}</p>
    </div>

    @php
        $kpi = $penempatan->penilaianKpi->where('status_validasi','diterima');

        $teknis = $kpi->filter(fn($i) =>
            strtolower($i->indikator->kategori->nama_kategori) == 'aspek teknis'
        );

        $nonTeknis = $kpi->filter(fn($i) =>
            strtolower($i->indikator->kategori->nama_kategori) == 'aspek non-teknis'
        );

        $groupTeknis = $teknis->groupBy(fn($i) => $i->indikator->nama_indikator);
        $groupNonTeknis = $nonTeknis->groupBy(fn($i) => $i->indikator->nama_indikator);

        $rataTeknis = $teknis->count() ? round($teknis->avg('nilai'),2) : 0;
        $rataNonTeknis = $nonTeknis->count() ? round($nonTeknis->avg('nilai'),2) : 0;
    @endphp

    {{-- ================= A. ASPEK TEKNIS ================= --}}
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <h2 class="font-semibold mb-6 text-gray-700">
            A. Aspek Teknis
        </h2>

        @forelse($groupTeknis as $indikator => $items)

            <div class="mb-6 border rounded-lg">
                <div class="bg-gray-100 px-4 py-2 font-semibold">
                    {{ $indikator }}
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Tanggal Penilaian</th>
                            <th class="px-4 py-2 text-center">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items->sortBy('periode_penilaian') as $item)
                        <tr class="border-b">
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($item->periode_penilaian)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                {{ $item->nilai }}
                            </td>
                        </tr>
                        @endforeach

                        <tr class="bg-gray-50 font-semibold">
                            <td class="px-4 py-2 text-right">
                                Rata-rata Indikator
                            </td>
                            <td class="px-4 py-2 text-center">
                                {{ round($items->avg('nilai'),2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @empty
            <p class="text-gray-500">Tidak ada data aspek teknis.</p>
        @endforelse

        <div class="mt-4 text-right font-bold">
            Rata-rata Aspek Teknis : {{ $rataTeknis }}
        </div>
    </div>


    {{-- ================= B. ASPEK NON TEKNIS ================= --}}
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-6 text-gray-700">
            B. Aspek Non Teknis
        </h2>

        @forelse($groupNonTeknis as $indikator => $items)

            <div class="mb-6 border rounded-lg">
                <div class="bg-gray-100 px-4 py-2 font-semibold">
                    {{ $indikator }}
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Tanggal Penilaian</th>
                            <th class="px-4 py-2 text-center">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items->sortBy('periode_penilaian') as $item)
                        <tr class="border-b">
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($item->periode_penilaian)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                {{ $item->nilai }}
                            </td>
                        </tr>
                        @endforeach

                        <tr class="bg-gray-50 font-semibold">
                            <td class="px-4 py-2 text-right">
                                Rata-rata Indikator
                            </td>
                            <td class="px-4 py-2 text-center">
                                {{ round($items->avg('nilai'),2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @empty
            <p class="text-gray-500">Tidak ada data aspek non teknis.</p>
        @endforelse

        <div class="mt-4 text-right font-bold">
            Rata-rata Aspek Non Teknis : {{ $rataNonTeknis }}
        </div>
    </div>

</div>
@endsection
