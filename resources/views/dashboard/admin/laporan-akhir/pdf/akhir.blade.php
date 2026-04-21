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
            margin-bottom: 30px;
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

        th, td {
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
            margin-top: 50px;
            width: 40%;
            float: right;
            text-align: center;
        }
    </style>
</head>
<body>

<h2>LAPORAN AKHIR PRAKTIK KERJA LAPANGAN</h2>

<div class="identitas">
    <p><strong>Nama Siswa</strong> : {{ $penempatan->siswa->nama_lengkap }}</p>
    <p><strong>Guru Pembimbing</strong> : {{ $penempatan->guru->nama_lengkap }}</p>
    <p><strong>Pembimbing Lapangan</strong> : {{ $penempatan->pembimbingLapangan->nama_lengkap }}</p>
    <p><strong>Masa PKL</strong> :
        {{ \Carbon\Carbon::parse($penempatan->tanggal_mulai)->translatedFormat('d F Y') }}
        -
        {{ \Carbon\Carbon::parse($penempatan->tanggal_selesai)->translatedFormat('d F Y') }}
    </p>
</div>

<div class="garis"></div>

<h3>3.1 Tempat Penugasan</h3>

<p><strong>Nama Perusahaan</strong> : {{ $penempatan->tempat->nama_perusahaan }}</p>
<p><strong>Alamat</strong> : {{ $penempatan->tempat->alamat }}</p>

<h3>3.2 Macam dan Uraian Kegiatan</h3>

<p>
Tanggal Pelaksanaan PKL :
{{ \Carbon\Carbon::parse($penempatan->tanggal_mulai)->translatedFormat('d F Y') }}
-
{{ \Carbon\Carbon::parse($penempatan->tanggal_selesai)->translatedFormat('d F Y') }}
</p>

<table>
    <thead>
        <tr>
            <th width="40">No</th>
            <th width="200">Hari dan Tanggal</th>
            <th>Jenis Kegiatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($penempatan->laporanKegiatan->sortBy('tanggal') as $index => $laporan)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">
                    {{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d F Y') }}
                </td>
                <td>
                    {{ $laporan->deskripsi_kegiatan ?? '-' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="center">Belum ada laporan kegiatan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="ttd">
    <p>Mengetahui,</p>
    <br><br><br>
    <strong>{{ $penempatan->pembimbingLapangan->nama_lengkap }}</strong><br>
    Pembimbing Lapangan
</div>

</body>
</html>
