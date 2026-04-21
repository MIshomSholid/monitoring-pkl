@extends('dashboard.guru-dashboard.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        {{-- HEADER + BUTTON --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Detail Presensi PKL
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

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Status Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penempatan->presensi->sortBy('tanggal') as $p)

                        @php
                            // Badge Jenis Presensi
                            $jenis = strtolower($p->jenis_presensi);

                            $bgJenis = 'bg-gray-100 text-gray-700';

                            if ($jenis == 'hadir') {
                                $bgJenis = 'bg-green-100 text-green-700';
                            } elseif ($jenis == 'izin') {
                                $bgJenis = 'bg-yellow-100 text-yellow-700';
                            } elseif ($jenis == 'alpha') {
                                $bgJenis = 'bg-red-100 text-red-700';
                            } elseif ($jenis == 'libur') {
                                $bgJenis = 'bg-gray-200 text-gray-600';
                            }

                            // Badge Status Validasi
                            $status = strtolower($p->status_validasi ?? 'menunggu');

                            $bgStatus = 'bg-blue-100 text-blue-700';

                            if ($status == 'diterima') {
                                $bgStatus = 'bg-green-100 text-green-700';
                            } elseif ($status == 'ditolak') {
                                $bgStatus = 'bg-red-100 text-red-700';
                            }
                        @endphp

                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}
                            </td>

                            {{-- JENIS --}}
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 text-xs rounded {{ $bgJenis }}">
                                    {{ ucfirst($jenis) }}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 text-xs rounded {{ $bgStatus }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>

                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
@endsection