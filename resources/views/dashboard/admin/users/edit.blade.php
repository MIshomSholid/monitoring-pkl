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
                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                    class="w-full border rounded px-3 py-2" required>
                @error('username')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full border rounded px-3 py-2" required>
                @error('email')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Role</label>
                <select name="role" class="w-full border rounded px-3 py-2" required>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="siswa" {{ $user->role == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru_pembimbing" {{ $user->role == 'guru_pembimbing' ? 'selected' : '' }}>
                        Guru Pembimbing
                    </option>
                    <option value="pembimbing_lapangan" {{ $user->role == 'pembimbing_lapangan' ? 'selected' : '' }}>
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
                <select name="is_active" class="w-full border rounded px-3 py-2">
                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Password Baru -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Password Baru <span class="text-gray-400 text-xs">(opsional)</span>
                </label>

                <div class="relative">
                    <input type="password" id="password" name="password" class="w-full border rounded px-3 py-2 pr-10"
                        placeholder="Kosongkan jika tidak diubah">

                    <button type="button" onclick="togglePassword('password','eye1-open','eye1-close')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">

                        <!-- Eye Open -->
                        <svg id="eye1-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                            c4.477 0 8.268 2.943 9.542 7
                            -1.274 4.057-5.065 7-9.542 7
                            -4.477 0-8.268-2.943-9.542-7z" />
                        </svg>

                        <!-- Eye Close -->
                        <svg id="eye1-close" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19
                            c-4.477 0-8.268-2.943-9.542-7
                            a9.956 9.956 0 012.042-3.362M6.343 6.343
                            A9.953 9.953 0 0112 5
                            c4.477 0 8.268 2.943 9.542 7
                            a9.96 9.96 0 01-4.132 5.411M15 12
                            a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />
                        </svg>
                    </button>
                </div>

                @error('password')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">
                    Konfirmasi Password
                </label>

                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full border rounded px-3 py-2 pr-10" placeholder="Ulangi password baru">

                    <button type="button" onclick="togglePassword('password_confirmation','eye2-open','eye2-close')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">

                        <!-- sama persis icon atas -->
                        <svg id="eye2-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                            c4.477 0 8.268 2.943 9.542 7
                            -1.274 4.057-5.065 7-9.542 7
                            -4.477 0-8.268-2.943-9.542-7z" />
                        </svg>

                        <svg id="eye2-close" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19
                            c-4.477 0-8.268-2.943-9.542-7
                            a9.956 9.956 0 012.042-3.362M6.343 6.343
                            A9.953 9.953 0 0112 5
                            c4.477 0 8.268 2.943 9.542 7
                            a9.96 9.96 0 01-4.132 5.411M15 12
                            a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Action -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                    Update Akun
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId, eyeOpenId, eyeCloseId) {
            const input = document.getElementById(inputId);
            const eyeOpen = document.getElementById(eyeOpenId);
            const eyeClose = document.getElementById(eyeCloseId);

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClose.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClose.classList.add('hidden');
            }
        }
    </script>
@endsection