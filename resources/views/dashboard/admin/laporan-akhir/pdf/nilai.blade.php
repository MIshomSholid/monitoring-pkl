<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            margin: 40px 50px;
            line-height: 1.6;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 16px;
            text-transform: uppercase;
        }

        h3 {
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .identitas p {
            margin: 3px 0;
        }

        .garis {
            border-top: 1px solid #000;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background-color: #f0d78c;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .ttd {
            margin-top: 60px;
            width: 40%;
            float: right;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

    <h2>LAPORAN PENILAIAN PRAKTEK KERJA LAPANGAN</h2>

    <div class="identitas">
        <p><strong>Nama Siswa</strong> : {{ $penempatan->siswa->nama_lengkap }}</p>
        <p><strong>Tempat PKL</strong> : {{ $penempatan->tempat->nama_perusahaan }}</p>
        <p><strong>Guru Pembimbing</strong> : {{ $penempatan->guru->nama_lengkap }}</p>
        <p><strong>Pembimbing Lapangan</strong> : {{ $penempatan->pembimbingLapangan->nama_lengkap }}</p>
        <p><strong>Masa PKL</strong> :
            {{ \Carbon\Carbon::parse($penempatan->tanggal_mulai)->translatedFormat('d F Y') }}
            -
            {{ \Carbon\Carbon::parse($penempatan->tanggal_selesai)->translatedFormat('d F Y') }}
        </p>
    </div>

    <div class="garis"></div>

    @php
        $kpi = $penempatan->penilaianKpi->where('status_validasi', 'diterima');

        $teknis = $kpi->filter(function ($item) {
            return strtolower($item->indikator->kategori->nama_kategori) == 'aspek teknis';
        });

        $nonTeknis = $kpi->filter(function ($item) {
            return strtolower($item->indikator->kategori->nama_kategori) == 'aspek non-teknis';
        });

        $rataTeknis = $teknis->count() > 0 ? round($teknis->avg('nilai'), 2) : 0;
        $rataNonTeknis = $nonTeknis->count() > 0 ? round($nonTeknis->avg('nilai'), 2) : 0;

        $rataTotal = round(($rataTeknis + $rataNonTeknis) / 2, 2);

        if (!function_exists('konversiHuruf')) {

            function konversiHuruf($nilai)
            {
                if ($nilai >= 92)
                    return 'A';
                elseif ($nilai >= 82)
                    return 'B';
                elseif ($nilai >= 75)
                    return 'C';
                else
                    return 'D';
            }

        }
    @endphp


    {{-- ================= A. ASPEK TEKNIS ================= --}}
    <h3>A. PENILAIAN ASPEK TEKNIS</h3>

    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Indikator</th>
                <th width="90">Nilai Angka</th>
                <th width="90">Nilai Huruf</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teknis as $index => $k)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $k->indikator->nama_indikator }}</td>
                    <td class="center">{{ number_format($k->nilai, 0) }}</td>
                    <td class="center">{{ konversiHuruf($k->nilai) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center">Tidak ada data aspek teknis.</td>
                </tr>
            @endforelse

            <tr>
                <td colspan="2" class="center"><strong>RATA-RATA TEKNIS</strong></td>
                <td class="center"><strong>{{ number_format($rataTeknis, 2) }}</strong></td>
                <td class="center"><strong>{{ konversiHuruf($rataTeknis) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- ================= B. ASPEK NON TEKNIS ================= --}}
    <h3>B. PENILAIAN ASPEK NON-TEKNIS</h3>

    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Indikator</th>
                <th width="90">Nilai Angka</th>
                <th width="90">Nilai Huruf</th>
            </tr>
        </thead>
        <tbody>

            @php
                // Kelompokkan berdasarkan indikator
                $nonTeknisGrouped = $nonTeknis->groupBy(function ($item) {
                    return $item->indikator->nama_indikator;
                });
            @endphp

            @forelse($nonTeknisGrouped as $namaIndikator => $items)

                @php
                    $rataIndikator = round($items->avg('nilai'), 2);
                @endphp

                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $namaIndikator }}</td>
                    <td class="center">{{ number_format($rataIndikator, 2) }}</td>
                    <td class="center">{{ konversiHuruf($rataIndikator) }}</td>
                </tr>

            @empty
                <tr>
                    <td colspan="4" class="center">Tidak ada data aspek non-teknis.</td>
                </tr>
            @endforelse

            {{-- Rata-rata keseluruhan Non Teknis --}}
            <tr>
                <td colspan="2" class="center"><strong>RATA-RATA NON-TEKNIS</strong></td>
                <td class="center"><strong>{{ number_format($rataNonTeknis, 2) }}</strong></td>
                <td class="center"><strong>{{ konversiHuruf($rataNonTeknis) }}</strong></td>
            </tr>

        </tbody>
    </table>


    {{-- ================= C. NILAI AKHIR ================= --}}
    <h3>C. NILAI AKHIR</h3>

    <table>
        <thead>
            <tr>
                <th>Rata-Rata Total KPI</th>
                <th width="120">Nilai Huruf</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center"><strong>{{ number_format($rataTotal, 2) }}</strong></td>
                <td class="center"><strong>{{ konversiHuruf($rataTotal) }}</strong></td>
            </tr>
        </tbody>
    </table>


    {{-- ================= KETERANGAN NILAI ================= --}}
    <h3>KETERANGAN NILAI</h3>

    <table>
        <tr>
            <td width="50%">92 - 100 = A (Sangat Baik)</td>
            <td>82 - 91 = B (Baik)</td>
        </tr>
        <tr>
            <td>75 - 81 = C (Cukup)</td>
            <td>
                < 75=D (Kurang)</td>
        </tr>
    </table>

    <div class="ttd">
        <p>Mengetahui,</p>
        <br><br><br>
        <strong>{{ $penempatan->pembimbingLapangan->nama_lengkap }}</strong><br>
        Pembimbing Lapangan
    </div>

</body>

</html>