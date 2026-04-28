@extends('dashboard.admin.layout')

@section('content')

<div class="p-6 bg-gray-50 min-h-screen">

    <h1 class="text-2xl font-bold mb-6">
        Tambah Data Admin
    </h1>

    <form action="{{ route('admin.admin-data.store') }}" method="POST" enctype="multipart/form-data"
        class="bg-white p-6 rounded shadow space-y-5">
        @csrf

        {{-- NAMA --}}
        <div>
            <label class="font-medium">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                class="w-full border rounded px-3 py-2 mt-1 @error('nama_lengkap') border-red-500 @enderror">

            @error('nama_lengkap')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- EMAIL --}}
        <div>
            <label class="font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="w-full border rounded px-3 py-2 mt-1 @error('email') border-red-500 @enderror">

            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- PASSWORD --}}
        <div>
            <label class="font-medium">Password</label>
            <input type="password" name="password"
                class="w-full border rounded px-3 py-2 mt-1 @error('password') border-red-500 @enderror">

            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- NO HP --}}
        <div>
            <label class="font-medium">No Telepon</label>
            <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                class="w-full border rounded px-3 py-2 mt-1 @error('no_telepon') border-red-500 @enderror">

            @error('no_telepon')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- ALAMAT --}}
        <div>
            <label class="font-medium">Alamat</label>
            <textarea name="alamat" rows="3"
                class="w-full border rounded px-3 py-2 mt-1 @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>

            @error('alamat')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- FOTO --}}
        <div>
            <label class="font-medium">Foto Profil</label>

            <input type="file" name="foto_profil" accept="image/*"
                onchange="previewImage(event)"
                class="w-full border rounded px-3 py-2 mt-1 @error('foto_profil') border-red-500 @enderror">

            @error('foto_profil')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            {{-- PREVIEW --}}
            <img id="preview" class="mt-3 w-24 h-24 object-cover rounded hidden">
        </div>

        {{-- BUTTON --}}
        <div class="flex gap-3 pt-4">
            <button type="submit"
                class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Simpan
            </button>

            <a href="{{ route('admin.admin-data.index') }}"
                class="px-5 py-2 bg-gray-300 rounded">
                Batal
            </a>
        </div>

    </form>

</div>

{{-- SCRIPT PREVIEW --}}
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