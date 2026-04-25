@extends('dashboard.admin.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Tambah Akun Pengguna</h1>

    <div class="bg-white border rounded p-6 max-w-xl">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <!-- Username -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" class="w-full border rounded px-3 py-2"
                    required>
                @error('username')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2"
                    required>
                @error('email')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Role</label>
                <select name="role" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru_pembimbing" {{ old('role') == 'guru_pembimbing' ? 'selected' : '' }}>
                        Guru Pembimbing
                    </option>
                    <option value="pembimbing_lapangan" {{ old('role') == 'pembimbing_lapangan' ? 'selected' : '' }}>
                        Pembimbing Lapangan
                    </option>
                </select>
                @error('role')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>

                <div class="relative">
                    <input type="password" id="password" name="password" class="w-full border rounded px-3 py-2 pr-10"
                        required>

                    <!-- Icon -->
                    <button type="button" onclick="togglePassword('password','eye1-open','eye1-close')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">

                        <!-- Eye Open -->
                        <svg id="eye1-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                                           c4.477 0 8.268 2.943 9.542 7
                                           -1.274 4.057-5.065 7-9.542 7
                                           -4.477 0-8.268-2.943-9.542-7z" />
                        </svg>

                        <!-- Eye Close -->
                        <svg id="eye1-close" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>

                @error('password')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>

                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full border rounded px-3 py-2 pr-10" required>

                    <button type="button" onclick="togglePassword('password_confirmation','eye2-open','eye2-close')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">

                        <svg id="eye2-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                                           c4.477 0 8.268 2.943 9.542 7
                                           -1.274 4.057-5.065 7-9.542 7
                                           -4.477 0-8.268-2.943-9.542-7z" />
                        </svg>

                        <svg id="eye2-close" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2" d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>

                @error('password_confirmation')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                    Simpan Akun
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