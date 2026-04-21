@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Akun Pengguna</h1>

<div class="bg-white border rounded p-6 max-w-xl">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <!-- Username -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Username</label>
            <input type="text"
                   name="username"
                   value="{{ old('username', $user->username) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
            @error('username')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $user->email) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
            @error('email')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role"
                    class="w-full border rounded px-3 py-2"
                    required>
                <option value="admin" {{ $user->role=='admin'?'selected':'' }}>Admin</option>
                <option value="siswa" {{ $user->role=='siswa'?'selected':'' }}>Siswa</option>
                <option value="guru_pembimbing" {{ $user->role=='guru_pembimbing'?'selected':'' }}>
                    Guru Pembimbing
                </option>
                <option value="pembimbing_lapangan" {{ $user->role=='pembimbing_lapangan'?'selected':'' }}>
                    Pembimbing Lapangan
                </option>
            </select>
            @error('role')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status Akun -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Status Akun</label>
            <select name="is_active"
                    class="w-full border rounded px-3 py-2">
                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <!-- Reset Password (Optional) -->
        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">
                Password Baru <span class="text-gray-400 text-xs">(opsional)</span>
            </label>
            <input type="password"
                   name="password"
                   class="w-full border rounded px-3 py-2"
                   placeholder="Kosongkan jika tidak diubah">
            @error('password')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 border rounded">
                Batal
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded">
                Update Akun
            </button>
        </div>
    </form>
</div>
@endsection
