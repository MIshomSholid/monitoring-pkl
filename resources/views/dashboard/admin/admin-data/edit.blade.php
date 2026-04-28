@extends('dashboard.admin.layout')

@section('content')

    <h1 class="text-2xl font-bold mb-6">Edit Data Admin</h1>

    <div class="bg-white border rounded-lg p-6 max-w-xl">
        <form method="POST" action="{{ route('admin.admin-data.update', $admin->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- AKUN --}}
            <div class="mb-4">
                <label class="block text-sm font-medium">Akun Admin</label>
                <select class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500" disabled>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $admin->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- NAMA --}}
            <div class="mb-4">
                <label class="block text-sm font-medium">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $admin->nama_lengkap) }}"
                    class="w-full border rounded px-3 py-2" required>

                @error('nama_lengkap')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- NO TELEPON --}}
            <div class="mb-4">
                <label class="block text-sm font-medium">No Telepon</label>
                <input type="text" name="no_telepon" value="{{ old('no_telepon', $admin->no_telepon) }}"
                    class="w-full border rounded px-3 py-2">

                @error('no_telepon')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- ALAMAT --}}
            <div class="mb-4">
                <label class="block text-sm font-medium">Alamat</label>
                <textarea name="alamat" rows="3"
                    class="w-full border rounded px-3 py-2">{{ old('alamat', $admin->alamat) }}</textarea>

                @error('alamat')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- FOTO --}}
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Foto Profil</label>

                {{-- FOTO LAMA --}}
                @if ($admin->foto_profil)
                    <img src="{{ $admin->foto_profil }}" class="w-24 h-24 rounded-full object-cover mb-2 border">
                @endif

                <input type="file" name="foto_profil" accept="image/*" class="w-full border rounded px-3 py-2">

                @error('foto_profil')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.admin-data.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>

                <button class="px-4 py-2 bg-indigo-600 text-white rounded">
                    Simpan Perubahan
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