@extends('dashboard.admin.layout')

@section('content')

<div class="p-6 bg-gray-50 min-h-screen">

    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">
        Edit Data Admin
    </h1>

    <div class="bg-white border rounded-lg p-6 max-w-3xl">

        <form action="{{ route('admin.admin-data.update', $admin->id) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- USER --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">
                        Akun Admin
                    </label>
                    <select name="user_id"
                            class="w-full border rounded px-3 py-2 text-sm"
                            disabled>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ $admin->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- NAMA --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Nama Lengkap
                    </label>
                    <input type="text"
                           name="nama_lengkap"
                           value="{{ old('nama_lengkap', $admin->nama_lengkap) }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                {{-- NO HP --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        No Telepon
                    </label>
                    <input type="text"
                           name="no_telepon"
                           value="{{ old('no_telepon', $admin->no_telepon) }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                {{-- ALAMAT --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">
                        Alamat
                    </label>
                    <textarea name="alamat"
                              rows="3"
                              class="w-full border rounded px-3 py-2 text-sm">{{ old('alamat', $admin->alamat) }}</textarea>
                </div>

                {{-- FOTO --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">
                        Foto Profil
                    </label>

                    <input type="file"
                           name="foto_profil"
                           accept="image/*"
                           onchange="previewImage(event)"
                           class="w-full border rounded px-3 py-2 text-sm">

                    {{-- preview lama --}}
                    @if($admin->foto_profil)
                        <img src="{{ $admin->foto_profil }}"
                             class="mt-3 w-20 h-20 object-cover rounded">
                    @endif

                    {{-- preview baru --}}
                    <img id="preview"
                         class="mt-3 w-20 h-20 object-cover rounded hidden">
                </div>

            </div>

            {{-- BUTTON --}}
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('admin.admin-data.index') }}"
                   class="px-4 py-2 border rounded text-sm">
                    Batal
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">
                    Update
                </button>
            </div>

        </form>
    </div>

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