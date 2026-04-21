@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Tambah Data Guru Pembimbing</h1>

<div class="bg-white border rounded-lg p-6 max-w-3xl">
    <form method="POST"
          action="{{ route('admin.guru.store') }}"
          enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- USER ACCOUNT --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">
                    Akun Login Guru
                </label>
                <select name="user_id"
                        class="w-full border rounded px-3 py-2"
                        required>
                    <option value="">-- Pilih Akun Guru --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->username }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- NIP --}}
            <div>
                <label class="block text-sm font-medium mb-1">NIP</label>
                <input type="text"
                       name="nip"
                       value="{{ old('nip') }}"
                       class="w-full border rounded px-3 py-2"
                       required>
                @error('nip')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- NAMA LENGKAP --}}
            <div>
                <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                <input type="text"
                       name="nama_lengkap"
                       value="{{ old('nama_lengkap') }}"
                       class="w-full border rounded px-3 py-2"
                       required>
                @error('nama_lengkap')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- MATA PELAJARAN --}}
            <div>
                <label class="block text-sm font-medium mb-1">
                    Mata Pelajaran
                </label>
                <input type="text"
                       name="mata_pelajaran"
                       value="{{ old('mata_pelajaran') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- NO TELEPON --}}
            <div>
                <label class="block text-sm font-medium mb-1">
                    No. Telepon
                </label>
                <input type="text"
                       name="no_telepon"
                       value="{{ old('no_telepon') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- FOTO PROFIL --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">
                    Foto Profil
                </label>
                <input type="file"
                       name="foto_profil"
                       accept="image/*"
                       class="w-full border rounded px-3 py-2">
                @error('foto_profil')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- ACTION --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.guru.index') }}"
               class="px-4 py-2 border rounded">
                Batal
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded">
                Simpan Data Guru
            </button>
        </div>

    </form>
</div>
@endsection
