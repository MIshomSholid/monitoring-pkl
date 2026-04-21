@extends('dashboard.admin.layout')

@section('content')
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                Rekap & Monitoring KPI Siswa
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Rekapitulasi nilai KPI siswa berdasarkan hasil penilaian pembimbing lapangan
                dan guru pembimbing selama pelaksanaan PKL
            </p>
        </div>

        {{-- FILTER --}}
        <form method="GET" action="{{ route('admin.rekap-kpi.index') }}"
            class="bg-white rounded-xl shadow p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div>
                <label class="text-sm font-medium text-gray-700">Nama Siswa</label>
                <input type="text" name="nama_siswa" value="{{ request('nama_siswa') }}" placeholder="Cari siswa..."
                    class="w-full mt-1 rounded-md border-gray-300 text-sm focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Tempat PKL</label>
                <select name="tempat_pkl_id" class="w-full mt-1 rounded-md border-gray-300 text-sm">
                    <option value="">Semua Tempat</option>
                    @foreach ($tempatList as $tempat)
                        <option value="{{ $tempat->id }}" {{ request('tempat_pkl_id') == $tempat->id ? 'selected' : '' }}>
                            {{ $tempat->nama_perusahaan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Periode PKL</label>
                <select name="periode_pkl_id" class="w-full mt-1 rounded-md border-gray-300 text-sm">
                    <option value="">Semua Periode</option>
                    @foreach ($periodeList as $periode)
                        <option value="{{ $periode->id }}" {{ request('periode_pkl_id') == $periode->id ? 'selected' : '' }}>
                            {{ $periode->nama_periode }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">
                    Filter
                </button>
                <a href="{{ route('admin.rekap-kpi.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-sm">
                    Reset
                </a>
            </div>
        </form>


        {{-- ================= DESKTOP TABLE ================= --}}
        <div class="hidden md:block bg-white rounded-xl shadow mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Siswa</th>
                            <th class="px-4 py-3 text-left">Tempat PKL</th>
                            <th class="px-4 py-3 text-left">Periode</th>
                            <th class="px-4 py-3 text-center">Teknis</th>
                            <th class="px-4 py-3 text-center">Non Teknis</th>
                            <th class="px-4 py-3 text-center">Presensi</th>
                            <th class="px-4 py-3 text-center">Laporan</th>
                            <th class="px-4 py-3 text-center">Total</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($rekap as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $row->siswa->nama_lengkap }}</td>
                                <td class="px-4 py-3">{{ $row->tempatPkl->nama_perusahaan }}</td>
                                <td class="px-4 py-3">{{ $row->periodePkl->nama_periode }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-indigo-600">
                                    {{ number_format($row->nilaiTeknis, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-green-600">
                                    {{ number_format($row->nilaiNonTeknis, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-yellow-600">
                                    {{ number_format($row->nilaiPresensi, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-blue-600">
                                    {{ number_format($row->nilaiLaporan, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-purple-600">
                                    {{ number_format($row->rataTotal, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                    Tidak ada data rekap KPI
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= MOBILE CARD ================= --}}
        <div class="space-y-4 md:hidden">
            @forelse ($rekap as $row)

                <div class="bg-white rounded-xl shadow p-4 space-y-4">

                    <div>
                        <p class="text-xs text-gray-500">Nama Siswa</p>
                        <p class="font-semibold text-gray-800">
                            {{ $row->siswa->nama_lengkap }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Tempat PKL</p>
                        <p>{{ $row->tempatPkl->nama_perusahaan }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Periode</p>
                        <p>{{ $row->periodePkl->nama_periode }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-3 border-t">

                        <div class="bg-indigo-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Teknis</p>
                            <p class="text-lg font-bold text-indigo-600">
                                {{ number_format($row->nilaiTeknis, 2) }}
                            </p>
                        </div>

                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Non Teknis</p>
                            <p class="text-lg font-bold text-green-600">
                                {{ number_format($row->nilaiNonTeknis, 2) }}
                            </p>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Presensi</p>
                            <p class="text-lg font-bold text-yellow-600">
                                {{ number_format($row->nilaiPresensi, 2) }}
                            </p>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Laporan</p>
                            <p class="text-lg font-bold text-blue-600">
                                {{ number_format($row->nilaiLaporan, 2) }}
                            </p>
                        </div>

                    </div>

                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500">Total Nilai</p>
                        <p class="text-lg font-bold text-purple-600">
                            {{ number_format($row->rataTotal, 2) }}
                        </p>
                    </div>

                </div>

            @empty
                <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
                    Tidak ada data rekap KPI
                </div>
            @endforelse
        </div>

    </div>
@endsection