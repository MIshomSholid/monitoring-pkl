@extends('dashboard.guru-dashboard.layout')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Detail Laporan Kegiatan
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

    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penempatan->laporanKegiatan->sortBy('tanggal') as $l)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">
                        {{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $l->deskripsi_kegiatan }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
