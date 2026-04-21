<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            margin: 40px 50px; /* disamakan */
            line-height: 1.6;  /* disamakan */
        }

        h2 {
            text-align: center;
            margin-bottom: 30px; /* disamakan */
            font-size: 16px;
            text-transform: uppercase;
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
            margin-top: 10px; /* disamakan */
        }

        /* UKURAN TABEL TIDAK DIUBAH */
        th,
        td {
            border: 1px solid #000;
            padding: 1px;        /* tetap */
            font-size: 7px;      /* tetap */
            text-align: center;
        }

        th {
            background-color: #f0d78c;
        }

        .bulan-col {
            width: 70px;
            font-size: 8px;
        }

        .tanggal-col {
            width: 12px;
        }

        .jumlah-col {
            width: 16px;
        }

        .keterangan {
            margin-top: 25px;
            font-size: 12px; /* disamakan dengan body */
        }

        .ttd {
            margin-top: 50px;
            width: 40%;
            float: right;
            text-align: center;
        }
    </style>
</head>

<body>

<h2>DAFTAR HADIR PRAKTIK KERJA LAPANGAN</h2>

<div class="identitas">
    <p><strong>Nama Siswa</strong> : {{ $penempatan->siswa->nama_lengkap }}</p>
    <p><strong>Tempat PKL</strong> : {{ $penempatan->tempat->nama_perusahaan }}</p>
    <p><strong>Masa PKL</strong> :
        {{ \Carbon\Carbon::parse($penempatan->tanggal_mulai)->translatedFormat('d F Y') }}
        -
        {{ \Carbon\Carbon::parse($penempatan->tanggal_selesai)->translatedFormat('d F Y') }}
    </p>
</div>

<div class="garis"></div>

@php
    $presensi = $penempatan->presensi->groupBy(function ($item) {
        return \Carbon\Carbon::parse($item->tanggal)->format('Y-m');
    });
@endphp

@foreach($presensi as $bulan => $dataBulan)

    @php
        $carbonBulan = \Carbon\Carbon::createFromFormat('Y-m', $bulan);

        $rekap = [
            'H' => 0,
            'A' => 0,
            'I' => 0,
            'T' => 0,
        ];

        $map = [];

        foreach ($dataBulan as $p) {

            $hari = \Carbon\Carbon::parse($p->tanggal)->day;
            $jenis = strtolower($p->jenis_presensi);

            if ($p->status_validasi === 'ditolak') {
                $map[$hari] = 'A';
                $rekap['A']++;
                continue;
            }

            if ($jenis === 'izin') {
                $map[$hari] = 'I';
                $rekap['I']++;
                continue;
            }

            if ($jenis === 'libur') {
                $map[$hari] = '-';
                continue;
            }

            if ($jenis === 'hadir') {

                $tanggal = \Carbon\Carbon::parse($p->tanggal);

                $jamMasuk = $tanggal->copy()->setTimeFromTimeString($penempatan->tempat->jam_masuk);

                $waktuPresensi = $p->waktu_presensi
                    ? $tanggal->copy()->setTimeFromTimeString($p->waktu_presensi)
                    : null;

                if (
                    $waktuPresensi &&
                    $waktuPresensi->greaterThan(
                        $jamMasuk->copy()->addMinutes($penempatan->tempat->toleransi_keterlambatan)
                    )
                ) {
                    $map[$hari] = 'T';
                    $rekap['T']++;
                } else {
                    $map[$hari] = 'H';
                    $rekap['H']++;
                }

                continue;
            }

            $map[$hari] = 'A';
            $rekap['A']++;
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="bulan-col">Bulan</th>
                <th colspan="31">Tanggal</th>
                <th colspan="4">Jumlah</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 31; $i++)
                    <th class="tanggal-col">{{ $i }}</th>
                @endfor
                <th class="jumlah-col">H</th>
                <th class="jumlah-col">A</th>
                <th class="jumlah-col">T</th>
                <th class="jumlah-col">I</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="bulan-col">
                    {{ $carbonBulan->translatedFormat('F') }}<br>
                    {{ $carbonBulan->translatedFormat('Y') }}
                </td>

                @for($i = 1; $i <= 31; $i++)
                    <td>{{ $map[$i] ?? '' }}</td>
                @endfor

                <td>{{ $rekap['H'] }}</td>
                <td>{{ $rekap['A'] }}</td>
                <td>{{ $rekap['T'] }}</td>
                <td>{{ $rekap['I'] }}</td>
            </tr>
        </tbody>
    </table>

@endforeach

<div class="keterangan">
    <strong>Keterangan:</strong><br>
    H : Hadir<br>
    A : Alpha<br>
    T : Terlambat<br>
    I : Izin
</div>

<div class="ttd">
    <p>Mengetahui,</p>
    <br><br><br>
    <strong>{{ $penempatan->pembimbingLapangan->nama_lengkap }}</strong><br>
    Pembimbing Lapangan
</div>

</body>
</html>
