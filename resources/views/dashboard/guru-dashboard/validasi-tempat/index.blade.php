@extends('dashboard.guru-dashboard.layout')

@section('content')
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">

        <h1 class="text-xl sm:text-2xl font-bold mb-6">
            Validasi Penempatan PKL
        </h1>

        {{-- ================= MENUNGGU ================= --}}
        <h2 class="text-lg font-semibold mb-4">Pengajuan Menunggu Validasi</h2>

        <div class="bg-white rounded-xl shadow mb-10 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Siswa</th>
                        <th class="px-4 py-3 text-left">Tempat</th>
                        <th class="px-4 py-3 text-left">Status Saat Ini</th>
                        <th class="px-4 py-3 text-left">Pengajuan</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menunggu as $item)

                        @php
                            $statusClass = $item->status == 'aktif'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700';

                            $pengajuanClass = $item->status_pengajuan == 'aktif'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700';
                        @endphp

                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $item->siswa->nama_lengkap }}</td>
                            <td class="px-4 py-3">{{ $item->tempat->nama_perusahaan }}</td>

                            {{-- STATUS SEKARANG --}}
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            {{-- PENGAJUAN --}}
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded {{ $pengajuanClass }}">
                                    {{ ucfirst($item->status_pengajuan) }}
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('guru.validasi-tempat.validate', $item->id) }}">
                                        @csrf
                                        <input type="hidden" name="status_validasi" value="diterima">
                                        <button class="px-3 py-1 bg-green-600 text-white rounded text-xs">
                                            Terima
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('guru.validasi-tempat.validate', $item->id) }}">
                                        @csrf
                                        <input type="hidden" name="status_validasi" value="ditolak">
                                        <button class="px-3 py-1 bg-red-600 text-white rounded text-xs">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">
                                Tidak ada pengajuan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- ================= DATA AKTIF ================= --}}
        <h2 class="text-lg font-semibold mb-4">Data Aktif (Sudah Disetujui)</h2>

        <div class="bg-white rounded-xl shadow mb-10 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Siswa</th>
                        <th class="px-4 py-3 text-left">Tempat</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aktif as $item)

                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $item->siswa->nama_lengkap }}</td>
                            <td class="px-4 py-3">{{ $item->tempat->nama_perusahaan }}</td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                    Aktif
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                    Disetujui
                                </span>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500">
                                Tidak ada data aktif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= DATA NONAKTIF ================= --}}
        <h2 class="text-lg font-semibold mb-4">Data Nonaktif (Sudah Disetujui)</h2>

        <div class="bg-white rounded-xl shadow mb-10 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Siswa</th>
                        <th class="px-4 py-3 text-left">Tempat</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nonaktif as $item)

                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $item->siswa->nama_lengkap }}</td>
                            <td class="px-4 py-3">{{ $item->tempat->nama_perusahaan }}</td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                    Nonaktif
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                    Disetujui
                                </span>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500">
                                Tidak ada data nonaktif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= RIWAYAT ================= --}}
        <h2 class="text-lg font-semibold mb-4">Riwayat Validasi</h2>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Siswa</th>
                        <th class="px-4 py-3 text-left">Tempat</th>
                        <th class="px-4 py-3 text-left">Pengajuan</th>
                        <th class="px-4 py-3 text-left">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)

                                <tr class="border-b">
                                    <td class="px-4 py-3">{{ $item->siswa->nama_lengkap }}</td>
                                    <td class="px-4 py-3">{{ $item->tempat->nama_perusahaan }}</td>

                                    <td class="px-4 py-3">
                                        {{ ucfirst($item->status_pengajuan) }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded
                                            {{ $item->status_validasi == 'diterima'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700' }}">
                                            {{ ucfirst($item->status_validasi) }}
                                        </span>
                                    </td>
                                </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500">
                                Belum ada riwayat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection