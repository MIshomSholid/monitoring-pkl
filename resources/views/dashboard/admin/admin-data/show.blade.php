@extends('dashboard.admin.layout')

@section('content')

<h1 class="text-2xl font-bold mb-6">Detail Admin</h1>

<div class="bg-white border rounded-lg p-6 max-w-3xl">

    <div class="flex items-start gap-6">

        {{-- FOTO --}}
        <div style="width:110px;height:150px;overflow:hidden;border-radius:6px;border:1px solid #e5e7eb;background:#f3f4f6;flex-shrink:0;">

            @if($admin->foto_profil)
                <img src="{{ $admin->foto_profil }}"
                     style="width:100%;height:100%;object-fit:cover;">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->nama_lengkap) }}&background=6366f1&color=fff"
                     style="width:100%;height:100%;object-fit:cover;">
            @endif

        </div>

        {{-- DATA --}}
        <div class="flex-1">
            <table class="text-sm w-full">

                <tr>
                    <td class="py-1 font-medium w-32">Nama</td>
                    <td>{{ $admin->nama_lengkap }}</td>
                </tr>

                <tr>
                    <td class="py-1 font-medium">Email</td>
                    <td>{{ $admin->user->email ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="py-1 font-medium">No Telepon</td>
                    <td>{{ $admin->no_telepon ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="py-1 font-medium">Alamat</td>
                    <td>{{ $admin->alamat ?? '-' }}</td>
                </tr>

            </table>
        </div>

    </div>

    {{-- BUTTON --}}
    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('admin.admin-data.index') }}"
           class="px-4 py-2 border rounded">
            Kembali
        </a>

        <a href="{{ route('admin.admin-data.edit', $admin->id) }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded">
            Edit
        </a>
    </div>

</div>

@endsection