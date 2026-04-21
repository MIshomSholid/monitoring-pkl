@extends('dashboard.guru-dashboard.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        <h1 class="text-2xl font-bold mb-6">
            Rekap Siswa Bimbingan
        </h1>


        {{-- ================= FILTER SISWA ================= --}}
        <div class="bg-white border rounded-lg p-4 mb-6">

            <form method="GET" class="flex flex-wrap items-end gap-4 text-sm">

                <div class="w-full sm:w-72">
                    <label class="block mb-1 font-medium text-gray-700">
                        Pilih Siswa
                    </label>

                    <select name="siswa_id" class="w-full border rounded px-3 py-2 h-10">

                        <option value="">-- Pilih Siswa --</option>

                        @foreach ($daftarSiswa as $siswa)

                            <option value="{{ $siswa->id }}" {{ request('siswa_id') == $siswa->id ? 'selected' : '' }}>

                                {{ $siswa->nama_lengkap }}

                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="flex gap-2">

                    <button type="submit" class="h-10 px-5 bg-indigo-600 text-white rounded hover:bg-indigo-700">

                        Tampilkan

                    </button>

                    <a href="{{ route('guru.rekap-siswa.index') }}"
                        class="h-10 px-5 bg-gray-100 rounded hover:bg-gray-200 flex items-center">

                        Reset

                    </a>

                </div>

            </form>

        </div>


        {{-- ================= JIKA BELUM PILIH SISWA ================= --}}
        @if(!request('siswa_id'))

            <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
                Silakan pilih siswa terlebih dahulu untuk melihat rekap.
            </div>

        @else


            <div class="space-y-8">

                @forelse ($penempatan as $item)

                    <div class="bg-white rounded-xl shadow p-6 space-y-6">

                        {{-- ================= HEADER SISWA ================= --}}
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">
                                {{ $item['penempatan']->siswa->nama_lengkap }}
                            </h2>

                            <p class="text-sm text-gray-500">
                                {{ $item['penempatan']->tempatPkl->nama_perusahaan }}
                            </p>
                        </div>


                        {{-- ================= RINGKASAN ================= --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">

                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-gray-500">Hadir</p>
                                <p class="text-xl font-bold text-green-700">
                                    {{ $item['hadir'] }}
                                </p>
                            </div>

                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-gray-500">Izin</p>
                                <p class="text-xl font-bold text-yellow-700">
                                    {{ $item['izin'] }}
                                </p>
                            </div>

                            <div class="bg-indigo-50 p-4 rounded-lg">
                                <p class="text-gray-500 mb-1">Laporan Kegiatan</p>

                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li>Diterima : <strong>{{ $item['laporan']['diterima'] }}</strong></li>
                                    <li>Menunggu : <strong>{{ $item['laporan']['menunggu'] }}</strong></li>
                                    <li>Ditolak : <strong>{{ $item['laporan']['ditolak'] }}</strong></li>
                                </ul>
                            </div>


                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-gray-500">Rata-rata KPI</p>
                                <p class="text-xl font-bold text-blue-700">
                                    {{ $item['kpi'] ?? '-' }}
                                </p>
                            </div>

                        </div>


                        {{-- ================= DETAIL PRESENSI ================= --}}
                        <div>

                            <h3 class="font-semibold text-gray-800 mb-3">
                                Detail Presensi
                            </h3>

                            <div class="overflow-x-auto">

                                <table class="w-full text-sm">

                                    <thead class="bg-gray-100 text-gray-700">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Tanggal</th>
                                            <th class="px-3 py-2 text-left">Jenis</th>
                                            <th class="px-3 py-2 text-left">Status</th>
                                            <th class="px-3 py-2 text-left">Bukti</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse ($presensi as $p)

                                            <tr class="border-b">

                                                <td class="px-3 py-2">
                                                    {{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}
                                                </td>

                                                <td class="px-3 py-2">

                                                    @php
                                                        if ($p->jenis_presensi === 'hadir') {
                                                            if ($p->isTerlambat()) {
                                                                $badge = 'bg-orange-100 text-orange-700';
                                                                $label = 'Hadir (Terlambat)';
                                                            } else {
                                                                $badge = 'bg-green-100 text-green-700';
                                                                $label = 'Hadir';
                                                            }
                                                        } elseif ($p->jenis_presensi === 'izin') {
                                                            $badge = 'bg-yellow-100 text-yellow-700';
                                                            $label = 'Izin';
                                                        } elseif ($p->jenis_presensi === 'alpha') {
                                                            $badge = 'bg-red-100 text-red-700';
                                                            $label = 'Alpha';
                                                        } else {
                                                            $badge = 'bg-gray-100 text-gray-700';
                                                            $label = ucfirst($p->jenis_presensi);
                                                        }
                                                    @endphp

                                                    <span class="px-2 py-1 text-xs rounded {{ $badge }}">
                                                        {{ $label }}
                                                    </span>

                                                </td>

                                                <td class="px-3 py-2">

                                                    @php $status = $p->status_validasi ?? 'menunggu'; @endphp

                                                    <span class="px-2 py-1 text-xs rounded
                                                    @if($status === 'diterima')
                                                        bg-green-100 text-green-700
                                                    @elseif($status === 'ditolak')
                                                        bg-red-100 text-red-700
                                                    @else
                                                        bg-blue-100 text-blue-700
                                                    @endif
                                                ">
                                                        {{ ucfirst($status) }}
                                                    </span>

                                                </td>

                                                <td class="px-3 py-2">

                                                    @if ($p->jenis_presensi === 'hadir' && $p->foto_presensi)
                                                        <a href="{{ asset('storage/' . $p->foto_presensi) }}" target="_blank"
                                                            class="text-indigo-600 hover:underline">
                                                            Lihat Foto
                                                        </a>

                                                    @elseif ($p->jenis_presensi === 'izin' && $p->bukti_izin)

                                                        <a href="{{ asset('storage/' . $p->bukti_izin) }}" target="_blank"
                                                            class="text-indigo-600 hover:underline">
                                                            Lihat Bukti
                                                        </a>

                                                    @else
                                                        -
                                                    @endif

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="4" class="px-3 py-4 text-center text-gray-500">
                                                    Belum ada data presensi
                                                </td>
                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                        <div class="mt-4">
                            {{ $presensi->links() }}
                        </div>

                    </div>

                @empty

                    <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
                        Tidak ada data siswa
                    </div>

                @endforelse

            </div>

        @endif

    </div>
@endsection