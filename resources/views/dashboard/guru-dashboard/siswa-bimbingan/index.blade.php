@extends('dashboard.guru-dashboard.layout')

@section('content')
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-6">

            {{-- ================= HEADER ================= --}}
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                    Data Siswa Bimbingan
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Daftar siswa yang berada dalam tanggung jawab bimbingan Anda
                </p>
            </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden md:block bg-white border rounded-lg shadow">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama Siswa</th>
                                <th class="px-4 py-3 text-left">Kelas / Jurusan</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Tempat PKL</th>
                                <th class="px-4 py-3 text-left">Alamat</th>
                                <th class="px-4 py-3 text-left">Periode</th>
                                <th class="px-4 py-3 text-left">Pembimbing</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($siswaBimbingan as $item)
                                @php
                                    $statusClass = match ($item->status) {
                                        'aktif' => 'bg-green-100 text-green-700',
                                        'selesai' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-red-100 text-red-700'
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">
                                        <a href="{{ route('guru.siswa-bimbingan.show', $item->id) }}"
                                            class="text-indigo-600 hover:underline">
                                            {{ $item->siswa->nama_lengkap }}
                                        </a>
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $item->siswa->kelas }} / {{ $item->siswa->jurusan }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $item->tempat->nama_perusahaan ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $item->tempat->alamat ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $item->periode->nama_periode ?? '-' }}
                                        <span class="text-gray-500">
                                            ({{ $item->periode->tahun_ajaran ?? '-' }})
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $item->pembimbingLapangan->nama_lengkap ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada data siswa bimbingan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                {{-- ================= MOBILE CARD ================= --}}
                <div class="md:hidden space-y-4">
                    @forelse ($siswaBimbingan as $item)
                        @php
                            $statusClass = match ($item->status) {
                                'aktif' => 'bg-green-100 text-green-700',
                                'selesai' => 'bg-blue-100 text-blue-700',
                                default => 'bg-red-100 text-red-700'
                            };
                        @endphp

                        <div class="bg-white border rounded-lg shadow p-4 text-sm space-y-2">

                            <div class="flex justify-between items-start">
                                <a href="{{ route('guru.siswa-bimbingan.show', $item->id) }}"
                                    class="font-semibold text-indigo-600 hover:underline">
                                    {{ $item->siswa->nama_lengkap }}
                                </a>

                                <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Kelas / Jurusan</p>
                                <p>{{ $item->siswa->kelas }} / {{ $item->siswa->jurusan }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Tempat PKL</p>
                                <p>{{ $item->tempat->nama_perusahaan ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Alamat</p>
                                <p>{{ $item->tempat->alamat ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Periode</p>
                                <p>
                                    {{ $item->periode->nama_periode ?? '-' }}
                                    <span class="text-gray-500 text-xs">
                                        ({{ $item->periode->tahun_ajaran ?? '-' }})
                                    </span>
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Pembimbing Lapangan</p>
                                <p>{{ $item->pembimbingLapangan->nama_lengkap ?? '-' }}</p>
                            </div>

                        </div>

                    @empty
                        <div class="bg-white border rounded-lg shadow p-6 text-center text-gray-500">
                            Belum ada data siswa bimbingan
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
@endsection