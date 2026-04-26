@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Detail Siswa</h1>

<div class="bg-white border rounded-lg p-6 max-w-3xl">

    {{-- FOTO + DATA --}}
    <div class="flex items-start gap-6">

        {{-- FOTO --}}
        <div style="width:110px;height:150px;overflow:hidden;border-radius:6px;border:1px solid #e5e7eb;background:#f3f4f6;flex-shrink:0;">

            @if($siswa->foto_profil)
                <img src="{{ $siswa->foto_profil }}"
                     style="width:100%;height:100%;object-fit:cover;">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=6366f1&color=fff"
                     style="width:100%;height:100%;object-fit:cover;">
            @endif

        </div>

        {{-- DATA --}}
        <div class="flex-1">
            <table class="text-sm w-full">

                <tr>
                    <td class="py-1 font-medium w-32">Nama</td>
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
                    <td>{{ $siswa->user->email }}</td>
                </tr>

                <tr>
                    <td class="py-1 font-medium">No Telepon</td>
                    <td>{{ $siswa->no_telepon ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="py-1 font-medium">Alamat</td>
                    <td>{{ $siswa->alamat ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="py-1 font-medium">Status</td>
                    <td>
                        @if ($siswa->user->is_active)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded">Nonaktif</span>
                        @endif
                    </td>
                </tr>

            </table>
        </div>

    </div>

    {{-- BUTTON --}}
    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('admin.siswa.index') }}"
           class="px-4 py-2 border rounded">
            Kembali
        </a>

        <a href="{{ route('admin.siswa.edit', $siswa) }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded">
            Edit
        </a>
    </div>

</div>
@endsection