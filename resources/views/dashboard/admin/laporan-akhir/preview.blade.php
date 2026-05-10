@extends('dashboard.admin.layout')

@section('content')

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">
            Preview Laporan Akhir PKL
        </h1>

        <a href="{{ route('admin.laporan-akhir.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700
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
    {{-- ================= DESKTOP TABLE ================= --}}
    <div class="hidden lg:block bg-white border rounded-lg shadow overflow-x-auto">

        <table class="w-full text-sm min-w-[1200px]">
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

                        $bgStatus = 'bg-yellow-100 text-yellow-700';

                        if ($status == 'diterima') {
                            $bgStatus = 'bg-green-100 text-green-700';
                        } elseif ($status == 'ditolak') {
                            $bgStatus = 'bg-red-100 text-red-700';
                        }
                    @endphp

                    <tr class="hover:bg-gray-50">

                        <td class="px-4 py-3 font-medium">
                            {{ $p->siswa->nama_lengkap }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $p->tempat->nama_perusahaan }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $p->guru->nama_lengkap }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $p->pembimbingLapangan->nama_lengkap }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs rounded-full {{ $bgStatus }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            {{ $p->laporanAkhir->catatan_validasi ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">

                            @if($status == 'diterima')

                                <form method="POST" action="{{ route('admin.laporan-akhir.export') }}">
                                    @csrf
                                    <input type="hidden" name="penempatan_id" value="{{ $p->id }}">
                                    <input type="hidden" name="jenis_laporan" value="{{ $jenis }}">

                                    <button class="px-3 py-2 bg-green-600 text-white rounded-md text-xs hover:bg-green-700">
                                        Cetak PDF
                                    </button>
                                </form>

                            @else

                                <button disabled class="px-3 py-2 bg-gray-300 text-gray-600 rounded-md text-xs">
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

    {{-- ================= MOBILE CARD ================= --}}
    <div class="lg:hidden space-y-4">

        @forelse ($penempatan as $p)

            @php
                $status = $p->laporanAkhir->status_validasi ?? 'menunggu';

                $bgStatus = 'bg-yellow-100 text-yellow-700';

                if ($status == 'diterima') {
                    $bgStatus = 'bg-green-100 text-green-700';
                } elseif ($status == 'ditolak') {
                    $bgStatus = 'bg-red-100 text-red-700';
                }
            @endphp

            <div class="bg-white border rounded-xl shadow-sm p-4">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            {{ $p->siswa->nama_lengkap }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ $p->tempat->nama_perusahaan }}
                        </p>
                    </div>

                    <span class="px-3 py-1 text-xs rounded-full {{ $bgStatus }}">
                        {{ ucfirst($status) }}
                    </span>

                </div>

                <div class="mt-4 space-y-2 text-sm">

                    <div>
                        <span class="font-medium text-gray-700">
                            Guru:
                        </span>
                        {{ $p->guru->nama_lengkap }}
                    </div>

                    <div>
                        <span class="font-medium text-gray-700">
                            Pembimbing:
                        </span>
                        {{ $p->pembimbingLapangan->nama_lengkap }}
                    </div>

                    <div>
                        <span class="font-medium text-gray-700">
                            Catatan:
                        </span>
                        {{ $p->laporanAkhir->catatan_validasi ?? '-' }}
                    </div>

                </div>

                <div class="mt-4">

                    @if($status == 'diterima')

                        <form method="POST" action="{{ route('admin.laporan-akhir.export') }}">
                            @csrf
                            <input type="hidden" name="penempatan_id" value="{{ $p->id }}">
                            <input type="hidden" name="jenis_laporan" value="{{ $jenis }}">

                            <button class="w-full py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition">
                                Cetak PDF
                            </button>
                        </form>

                    @else

                        <button disabled class="w-full py-2 bg-gray-300 text-gray-600 rounded-lg text-sm">
                            Belum Disetujui
                        </button>

                    @endif

                </div>

            </div>

        @empty

            <div class="bg-white border rounded-xl p-6 text-center text-gray-500">
                Tidak ada data penempatan siswa.
            </div>

        @endforelse

    </div>

    {{-- ================= CATATAN ================= --}}
    <div class="mt-6 bg-gray-50 border rounded-lg p-4 text-sm text-gray-600">
        <strong>Keterangan:</strong>
        <ul class="list-disc ml-5 mt-1 space-y-1">
            <li>Tombol <strong>Cetak PDF</strong> hanya aktif jika laporan sudah <strong>Disetujui Guru Pembimbing</strong>.
            </li>
            <li>Setiap cetak menghasilkan laporan untuk <strong>1 siswa</strong>.</li>
            <li>Format laporan mengikuti template resmi PKL.</li>
        </ul>
    </div>

@endsection