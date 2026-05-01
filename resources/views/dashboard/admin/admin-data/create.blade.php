@extends('dashboard.admin.layout')

@section('content')

<h1 class="text-2xl font-bold mb-6">Tambah Data Admin</h1>

<div class="bg-white border rounded-lg p-6 max-w-xl">

    <form action="{{ route('admin.admin-data.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- USER --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Pilih Akun Admin</label>
            <select name="user_id"
                class="w-full border rounded px-3 py-2 @error('user_id') border-red-500 @enderror"
                required>
                <option value="">-- Pilih User --</option>

                @foreach ($users as $user)
                    <option value="{{ $user->id }}"
                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->email }}
                    </option>
                @endforeach
            </select>

            @error('user_id')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- NAMA --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Nama Lengkap</label>
            <input type="text" name="nama_lengkap"
                value="{{ old('nama_lengkap') }}"
                class="w-full border rounded px-3 py-2 @error('nama_lengkap') border-red-500 @enderror">

            @error('nama_lengkap')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- NO HP --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">No Telepon</label>
            <input type="text" name="no_telepon"
                value="{{ old('no_telepon') }}"
                class="w-full border rounded px-3 py-2">
        </div>

        {{-- ALAMAT --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Alamat</label>
            <textarea name="alamat" rows="3"
                class="w-full border rounded px-3 py-2">{{ old('alamat') }}</textarea>
        </div>

        {{-- FOTO --}}
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Foto Profil</label>

            <input type="file" name="foto_profil" accept="image/*"
                onchange="previewImage(event)"
                class="w-full border rounded px-3 py-2">

            <img id="preview" class="mt-3 w-24 h-24 object-cover rounded hidden">
        </div>

        {{-- BUTTON --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.admin-data.index') }}"
                class="px-4 py-2 border rounded">
                Batal
            </a>

            <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Simpan Data Admin
            </button>
        </div>

    </form>
</div>

<script>
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');

    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
        preview.classList.remove('hidden');
    }
}
</script>

@endsection