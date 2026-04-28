@extends('dashboard.admin.layout')

@section('content')

<div class="p-6 bg-gray-50 min-h-screen">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">
            Detail Admin
        </h1>

        <div class="flex gap-2">
            <a href="{{ route('admin.admin-data.edit', $admin->id) }}"
               class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                Edit
            </a>

            <a href="{{ route('admin.admin-data.index') }}"
               class="px-4 py-2 bg-gray-300 rounded">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded shadow p-6 flex flex-col md:flex-row gap-6">

        {{-- FOTO --}}
        <div class="flex justify-center md:justify-start">
            @if($admin->foto_profil)
                <img src="{{ asset('storage/' . $admin->foto_profil) }}"
                     class="w-40 h-40 rounded-full object-cover border">
            @else
                <div class="w-40 h-40 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                    No Image
                </div>
            @endif
        </div>

        {{-- DATA --}}
        <div class="flex-1 space-y-4 text-sm">

            <div>
                <p class="text-gray-500">Nama Lengkap</p>
                <p class="font-semibold text-lg">
                    {{ $admin->nama_lengkap }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Email</p>
                <p>
                    {{ $admin->user->email ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">No Telepon</p>
                <p>
                    {{ $admin->no_telepon ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Alamat</p>
                <p>
                    {{ $admin->alamat ?? '-' }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4 text-xs text-gray-500">
                <div>
                    <p>Dibuat</p>
                    <p>{{ $admin->created_at ? $admin->created_at->format('d M Y H:i') : '-' }}</p>
                </div>

                <div>
                    <p>Diperbarui</p>
                    <p>{{ $admin->updated_at ? $admin->updated_at->format('d M Y H:i') : '-' }}</p>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection