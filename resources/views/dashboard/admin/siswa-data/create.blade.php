@extends('dashboard.admin.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Tambah Data Siswa</h1>

    <div class="bg-white border rounded-lg p-6 max-w-3xl">
        <form method="POST"
              action="{{ route('admin.siswa.store') }}"
              enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- USER ACCOUNT --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">
                        Akun Login (User)
                    </label>
                    <select name="user_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Akun --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->username }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NIS --}}
                <div>
                    <label class="block text-sm font-medium mb-1">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis') }}"
                        class="w-full border rounded px-3 py-2" required>
                    @error('nis')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NAMA --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                        class="w-full border rounded px-3 py-2" required>
                    @error('nama_lengkap')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- KELAS --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Kelas</label>
                    <input type="text" name="kelas" value="{{ old('kelas') }}"
                        class="w-full border rounded px-3 py-2" required>
                </div>

                {{-- JURUSAN --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Jurusan</label>
                    <input type="text" name="jurusan" value="{{ old('jurusan') }}"
                        class="w-full border rounded px-3 py-2" required>
                </div>

                {{-- NO TELP --}}
                <div>
                    <label class="block text-sm font-medium mb-1">No. Telepon</label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                        class="w-full border rounded px-3 py-2">
                </div>

                {{-- FOTO PROFIL --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Foto Profil</label>
                    <input type="file"
                           name="foto_profil"
                           accept="image/*"
                           class="w-full border rounded px-3 py-2">
                    @error('foto_profil')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ALAMAT --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Alamat</label>
                    <textarea name="alamat" rows="3"
                        class="w-full border rounded px-3 py-2">{{ old('alamat') }}</textarea>
                </div>

            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('admin.siswa.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                    Simpan Data Siswa
                </button>
            </div>
        </form>
    </div>
@endsection
