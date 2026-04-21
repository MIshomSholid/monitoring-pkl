@extends('dashboard.admin.layout')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">
        Preview Laporan Akhir PKL
    </h1>

    <a href="{{ route('admin.laporan-akhir.index') }}"
        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700
               rounded-md text-sm hover:bg-gray-300 transition">
        Kembali
    </a>
</div>

{{-- ================= RINGKASAN ================= --}}
<div class="bg-white border rounded-lg p-5 mb-6 text-sm text-gray-700 shadow-sm">
    <p>
        <strong>Periode PKL:</strong>
        {{ $periode->nama_periode }} ({{ $periode->tahun_ajaran }})
        <br>

        <strong>Jenis Laporan:</strong>
        @if($jenis == 'presensi')
            Rekap Presensi
        @elseif($jenis == 'nilai')
            Rekap Nilai
        @elseif($jenis == 'akhir')
            Rekap Laporan Akhir
        @endif
    </p>
</div>

{{-- ================= TABEL SISWA ================= --}}
<div class="bg-white border rounded-lg shadow overflow-x-auto">

    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-3 text-left">Siswa</th>
                <th class="px-4 py-3 text-left">Tempat PKL</th>
                <th class="px-4 py-3 text-left">Guru Pembimbing</th>
                <th class="px-4 py-3 text-left">Pembimbing Lapangan</th>
                <th class="px-4 py-3 text-center">Status Validasi Guru</th>
                <th class="px-4 py-3 text-left">Catatan Guru</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @forelse ($penempatan as $p)

                @php
                    $status = $p->laporanAkhir->status_validasi ?? 'menunggu';

                    $bgStatus = 'bg-yellow-100 rounded-md text-yellow-700';

                    if ($status == 'diterima') {
                        $bgStatus = 'bg-green-100 rounded-md text-green-700';
                    } elseif ($status == 'ditolak') {
                        $bgStatus = 'bg-red-100 rounded-md text-red-700';
                    }
                @endphp

                <tr class="hover:bg-gray-50">

                    {{-- SISWA --}}
                    <td class="px-4 py-3 font-medium">
                        {{ $p->siswa->nama_lengkap }}
                    </td>

                    {{-- TEMPAT --}}
                    <td class="px-4 py-3">
                        {{ $p->tempat->nama_perusahaan }}
                    </td>

                    {{-- GURU --}}
                    <td class="px-4 py-3">
                        {{ $p->guru->nama_lengkap }}
                    </td>

                    {{-- PEMBIMBING LAPANGAN --}}
                    <td class="px-4 py-3">
                        {{ $p->pembimbingLapangan->nama_lengkap }}
                    </td>

                    {{-- STATUS VALIDASI --}}
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 text-xs rounded-full {{ $bgStatus }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>

                    {{-- CATATAN GURU --}}
                    <td class="px-4 py-3 text-sm text-gray-700">
                        @if($p->laporanAkhir)
                            {{ $p->laporanAkhir->catatan_validasi ?? '-' }}

                            @if($p->laporanAkhir->validated_at)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($p->laporanAkhir->validated_at)->translatedFormat('d F Y H:i') }}
                                </div>
                            @endif
                        @else
                            -
                        @endif
                    </td>

                    {{-- AKSI CETAK --}}
                    <td class="px-4 py-3 text-center">

                        @if($status == 'diterima')

                            <form method="POST" action="{{ route('admin.laporan-akhir.export') }}">
                                @csrf
                                <input type="hidden" name="penempatan_id" value="{{ $p->id }}">
                                <input type="hidden" name="jenis_laporan" value="{{ $jenis }}">

                                <button class="inline-flex items-center px-3 py-1.5 bg-green-600
                                               text-white rounded-md text-xs hover:bg-green-700 transition">
                                    Cetak PDF
                                </button>
                            </form>

                        @else

                            <button disabled
                                class="inline-flex items-center px-3 py-1.5 bg-gray-300
                                       text-gray-600 rounded-md text-xs cursor-not-allowed">
                                Belum Disetujui
                            </button>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                        Tidak ada data penempatan siswa.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

</div>

{{-- ================= CATATAN ================= --}}
<div class="mt-6 bg-gray-50 border rounded-lg p-4 text-sm text-gray-600">
    <strong>Keterangan:</strong>
    <ul class="list-disc ml-5 mt-1 space-y-1">
        <li>Tombol <strong>Cetak PDF</strong> hanya aktif jika laporan sudah <strong>Disetujui Guru Pembimbing</strong>.</li>
        <li>Setiap cetak menghasilkan laporan untuk <strong>1 siswa</strong>.</li>
        <li>Format laporan mengikuti template resmi PKL.</li>
    </ul>
</div>

@endsection
