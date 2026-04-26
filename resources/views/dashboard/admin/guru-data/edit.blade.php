@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Data Guru Pembimbing</h1>

<div class="bg-white border rounded-lg p-6 max-w-xl">
    <form method="POST"
          action="{{ route('admin.guru.update', $guru) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- NIP --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">NIP</label>
            <input type="text"
                   name="nip"
                   value="{{ old('nip', $guru->nip) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
            @error('nip')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- NAMA LENGKAP --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Nama Lengkap</label>
            <input type="text"
                   name="nama_lengkap"
                   value="{{ old('nama_lengkap', $guru->nama_lengkap) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
            @error('nama_lengkap')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- MATA PELAJARAN --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Mata Pelajaran</label>
            <input type="text"
                   name="mata_pelajaran"
                   value="{{ old('mata_pelajaran', $guru->mata_pelajaran) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- NO TELEPON --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">No Telepon</label>
            <input type="text"
                   name="no_telepon"
                   value="{{ old('no_telepon', $guru->no_telepon) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- FOTO PROFIL --}}
        <div class="mb-6">
            <label class="block text-sm font-medium">Foto Profil</label>
            <input type="file"
                   name="foto_profil"
                   accept="image/*"
                   class="w-full border rounded px-3 py-2">

            @if ($guru->foto_profil)
                <p class="text-sm text-gray-500 mt-1">Foto saat ini:</p>
                <img src="{{ $guru->foto_profil }}"
                     alt="Foto Profil Guru"
                     class="h-20 mt-2 rounded border">
            @endif

            @error('foto_profil')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.guru.index') }}"
               class="px-4 py-2 border rounded">
                Batal
            </a>

            <button class="px-4 py-2 bg-indigo-600 text-white rounded">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
