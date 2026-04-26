@extends('dashboard.guru-dashboard.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Detail Siswa Bimbingan
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Informasi lengkap data diri siswa dan penempatan PKL
            </p>
        </div>

        <div class="bg-white rounded-xl shadow p-6 space-y-6">

            {{-- DATA SISWA --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-3">
                    Data Diri Siswa
                </h2>

                <div class="flex flex-col md:flex-row gap-6">

                    {{-- FOTO 3x4 --}}
                    {{-- FOTO 3x4 --}}
                    <div
                        style="width:110px;height:150px;overflow:hidden;border-radius:6px;border:1px solid #e5e7eb;background:#f3f4f6;flex-shrink:0;">

                        @php
                            $foto = $penempatan->siswa->foto_profil
                                ? $penempatan->siswa->foto_profil
                                : 'https://ui-avatars.com/api/?name=' . urlencode($penempatan->siswa->nama_lengkap) . '&background=6366f1&color=fff';
                        @endphp

                        <img src="{{ $foto }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>

                    {{-- DATA TEXT --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm flex-1">

                        <div>
                            <span class="text-gray-500">Nama Lengkap</span>
                            <p class="font-medium">{{ $penempatan->siswa->nama_lengkap }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500">NIS</span>
                            <p class="font-medium">{{ $penempatan->siswa->nis }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500">Kelas</span>
                            <p class="font-medium">{{ $penempatan->siswa->kelas }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500">Jurusan</span>
                            <p class="font-medium">{{ $penempatan->siswa->jurusan }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500">No. Telepon</span>
                            <p class="font-medium">{{ $penempatan->siswa->no_telepon ?? '-' }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500">Alamat</span>
                            <p class="font-medium">{{ $penempatan->siswa->alamat ?? '-' }}</p>
                        </div>

                    </div>
                </div>
            </div>

            <hr>

            {{-- DATA PENEMPATAN PKL --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-3">
                    Informasi Penempatan PKL
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Tempat PKL</span>
                        <p class="font-medium">{{ $penempatan->tempat->nama_perusahaan }}</p>
                    </div>

                    <div>
                        <span class="text-gray-500">Alamat Tempat PKL</span>
                        <p class="font-medium">{{ $penempatan->tempat->alamat }}</p>
                    </div>

                    <div>
                        <span class="text-gray-500">Periode PKL</span>
                        <p class="font-medium">
                            {{ $penempatan->periode->nama_periode }}
                            ({{ $penempatan->periode->tahun_ajaran }})
                        </p>
                    </div>

                    <div>
                        <span class="text-gray-500">Status PKL</span>
                        <p class="font-medium">{{ ucfirst($penempatan->status) }}</p>
                    </div>
                </div>
            </div>

            <hr>

            {{-- DATA PEMBIMBING LAPANGAN --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-3">
                    Data Pembimbing Lapangan
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Nama Lengkap</span>
                        <p class="font-medium">
                            {{ $penempatan->pembimbingLapangan->nama_lengkap ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-gray-500">Jabatan</span>
                        <p class="font-medium">
                            {{ $penempatan->pembimbingLapangan->jabatan ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-gray-500">No. Telepon</span>
                        <p class="font-medium">
                            {{ $penempatan->pembimbingLapangan->no_telepon ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-gray-500">Email Perusahaan</span>
                        <p class="font-medium">
                            {{ $penempatan->pembimbingLapangan->email_perusahaan ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="pt-4">
                <a href="{{ route('guru.siswa-bimbingan.index') }}"
                    class="inline-block px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                    Kembali
                </a>
            </div>

        </div>
    </div>
@endsection