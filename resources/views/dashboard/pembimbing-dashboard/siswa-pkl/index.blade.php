@extends('dashboard.pembimbing-dashboard.layout')

@section('content')
<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">
        Data Siswa PKL
    </h1>

    {{-- DESKRIPSI --}}
    <div class="bg-white p-4 sm:p-5 rounded-lg shadow mb-6 text-sm text-gray-700">
        Halaman ini menampilkan daftar siswa yang sedang melaksanakan PKL di perusahaan
        berdasarkan penempatan yang telah ditentukan oleh Admin.
        Informasi bersifat <span class="font-medium">read-only</span> dan digunakan
        sebagai referensi dalam proses pembimbingan siswa.
    </div>

    {{-- ================= DESKTOP TABLE ================= --}}
    <div class="hidden md:block bg-white rounded-lg shadow">
        <table class="w-full text-sm text-gray-700">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Nama Siswa</th>
                    <th class="px-6 py-3 text-left font-semibold">Jurusan</th>
                    <th class="px-6 py-3 text-left font-semibold">Periode PKL</th>
                    <th class="px-6 py-3 text-left font-semibold">Guru Pembimbing</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($data as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            {{ $item->siswa->nama_lengkap }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->siswa->jurusan }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->periodePkl->nama_periode }}
                            <span class="text-gray-500 text-xs">
                                ({{ $item->periode->tahun_ajaran ?? '-' }})
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            {{ $item->guruPembimbing->nama_lengkap }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs
                                {{ $item->status == 'aktif' ? 'bg-green-100 text-green-700' :
                                ($item->status == 'selesai' ? 'bg-blue-100 text-blue-700' :
                                'bg-red-100 text-red-700') }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                            Tidak ada data siswa PKL yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= MOBILE CARD ================= --}}
    <div class="md:hidden space-y-4">
        @forelse ($data as $item)
            <div class="bg-white rounded-lg shadow p-4 text-sm space-y-2">

                <div>
                    <p class="text-xs text-gray-500">Nama Siswa</p>
                    <p class="font-medium text-gray-800">
                        {{ $item->siswa->nama_lengkap }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Jurusan</p>
                    <p>{{ $item->siswa->jurusan }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Periode PKL</p>
                    <p>
                        {{ $item->periodePkl->nama_periode }}
                        <span class="text-gray-500 text-xs">
                            ({{ $item->periode->tahun_ajaran ?? '-' }})
                        </span>
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Guru Pembimbing</p>
                    <p>{{ $item->guruPembimbing->nama_lengkap }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Status PKL</p>
                    <span class="inline-block mt-1 px-2 py-1 text-xs
                        {{ $item->status == 'aktif' ? 'bg-green-100 text-green-700' :
                            ($item->status == 'selesai' ? 'bg-blue-100 text-blue-700' :
                            'bg-red-100 text-red-700') }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                Tidak ada data siswa PKL yang terdaftar.
            </div>
        @endforelse
    </div>

    {{-- CATATAN --}}
    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-600">
        <p class="font-medium mb-2">Catatan:</p>
        <ul class="list-disc ml-5 space-y-1">
            <li>Data siswa ditentukan oleh Admin melalui penempatan PKL.</li>
            <li>Pembimbing Lapangan tidak dapat mengubah data penempatan.</li>
            <li>Data ini digunakan sebagai dasar validasi presensi dan penilaian KPI.</li>
        </ul>
    </div>

</div>
@endsection
