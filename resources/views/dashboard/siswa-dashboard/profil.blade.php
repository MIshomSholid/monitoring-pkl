@extends('dashboard.siswa-dashboard.layout')

@section('content')

    <div class="p-6 bg-gray-50 min-h-screen">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Profil & Informasi PKL
        </h1>


        {{-- ================= DATA SISWA ================= --}}
        <div class="bg-white border rounded-lg p-6 mb-6">

            <h2 class="text-lg font-semibold mb-4">
                Data Diri Siswa
            </h2>

            <div class="flex items-start gap-6">

                {{-- FOTO 3x4 --}}
                <div
                    style="width:110px;height:150px;overflow:hidden;border-radius:6px;border:1px solid #e5e7eb;background:#f3f4f6;flex-shrink:0;">

                    @if($siswa->foto_profil)
                        <img src="{{ asset('storage/' . $siswa->foto_profil) }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=6366f1&color=fff"
                            style="width:100%;height:100%;object-fit:cover;">
                    @endif

                </div>


                {{-- DATA SISWA --}}
                <div class="flex-1">

                    <table class="text-sm w-full">

                        <tr>
                            <td class="py-1 font-medium w-40">Nama Lengkap</td>
                            <td>{{ $siswa->nama_lengkap }}</td>
                        </tr>

                        <tr>
                            <td class="py-1 font-medium">NIS</td>
                            <td>{{ $siswa->nis }}</td>
                        </tr>

                        <tr>
                            <td class="py-1 font-medium">Kelas</td>
                            <td>{{ $siswa->kelas }}</td>
                        </tr>

                        <tr>
                            <td class="py-1 font-medium">Jurusan</td>
                            <td>{{ $siswa->jurusan }}</td>
                        </tr>

                        <tr>
                            <td class="py-1 font-medium">Email</td>
                            <td>{{ $user->email }}</td>
                        </tr>

                        <tr>
                            <td class="py-1 font-medium">No Telepon</td>
                            <td>{{ $siswa->no_telepon ?? '-' }}</td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>



        {{-- ================= PENEMPATAN PKL ================= --}}
        <div class="bg-white border rounded-lg p-6 mb-6">

            <h2 class="text-lg font-semibold mb-4">
                Penempatan PKL
            </h2>

            @if ($penempatan)

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                    <div>
                        <p class="font-medium">Tempat PKL</p>
                        <p>{{ optional($penempatan->tempatPkl)->nama_perusahaan ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="font-medium">Alamat Tempat PKL</p>
                        <p>{{ optional($penempatan->tempatPkl)->alamat ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="font-medium">Periode PKL</p>
                        <p>
                            {{ optional($penempatan->periodePkl)->nama_periode ?? '-' }}
                            {{ optional($penempatan->periodePkl)->tahun_ajaran ? '(' . $penempatan->periodePkl->tahun_ajaran . ')' : '' }}
                        </p>
                    </div>

                    <div>
                        <p class="font-medium">Durasi PKL</p>
                        <p>
                            @if($penempatan->periodePkl)
                                {{ optional($penempatan->periodePkl->tanggal_mulai)->format('d M Y') }}
                                s/d
                                {{ optional($penempatan->periodePkl->tanggal_selesai)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="font-medium">Guru Pembimbing</p>
                        <p>{{ optional($penempatan->guruPembimbing)->nama_lengkap ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="font-medium">Pembimbing Lapangan</p>
                        <p>{{ optional($penempatan->pembimbingLapangan)->nama_lengkap ?? '-' }}</p>
                    </div>

                </div>

            @else

                <p class="text-red-600 text-sm">
                    Data penempatan PKL belum tersedia atau belum aktif.
                </p>

            @endif

        </div>



        {{-- ================= INFORMASI PKL ================= --}}
        <div class="bg-white border rounded-lg p-6">

            <h2 class="text-lg font-semibold mb-4">
                Informasi & Jadwal PKL
            </h2>

            @if ($informasiPkl->count())

                <div class="space-y-4">

                    @foreach ($informasiPkl as $info)

                                <div class="border rounded-md p-4">

                                    <div class="flex items-center justify-between mb-2">

                                        <h3 class="font-semibold">
                                            {{ $info->judul }}
                                        </h3>

                                        <span class="text-xs px-2 py-1 rounded
                        {{ $info->tipe === 'jadwal' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $info->tipe === 'informasi' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $info->tipe === 'ketentuan' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                            {{ ucfirst($info->tipe) }}
                                        </span>

                                    </div>

                                    <p class="text-sm text-gray-500 mb-2">
                                        {{ $info->tanggal_publish->format('d M Y') }}
                                    </p>

                                    <p class="text-sm text-gray-700 {{ strlen($info->konten) > 300 ? 'line-clamp-3' : '' }}">
                                        {{ $info->konten }}
                                    </p>

                                </div>

                    @endforeach

                </div>

            @else

                <p class="text-sm text-gray-500">
                    Belum ada informasi PKL yang dipublikasikan.
                </p>

            @endif
        </div>
    </div>

@endsection