@extends('dashboard.admin.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit Data Siswa</h1>

    <div class="bg-white border rounded-lg p-6 max-w-xl">
        <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium">NIS</label>
                <input type="text" name="nis" value="{{ old('nis', $siswa->nis) }}" class="w-full border rounded px-3 py-2"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Kelas</label>
                <input type="text" name="kelas" value="{{ old('kelas', $siswa->kelas) }}"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Jurusan</label>
                <input type="text" name="jurusan" value="{{ old('jurusan', $siswa->jurusan) }}"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">No Telepon</label>
                <input type="text" name="no_telepon" value="{{ old('no_telepon', $siswa->no_telepon) }}"
                    class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">

                <label class="block text-sm font-medium mb-2">
                    Foto Profil
                </label>

                @if($siswa->foto_profil)
                    <img src="{{ asset('storage/' . $siswa->foto_profil) }}"
                        class="w-24 h-24 rounded-full object-cover mb-2 border">
                @endif

                <input type="file" name="foto_profil" accept="image/*" class="w-full border rounded px-3 py-2">

            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium">Alamat</label>
                <textarea name="alamat" class="w-full border rounded px-3 py-2"
                    rows="3">{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.siswa.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>

                <button class="px-4 py-2 bg-indigo-600 text-white rounded">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection