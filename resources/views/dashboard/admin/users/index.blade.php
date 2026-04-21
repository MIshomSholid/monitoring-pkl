@extends('dashboard.admin.layout')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
<div class="flex flex-col gap-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">
            Manajemen Pengguna
        </h1>

        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center justify-center
                  px-4 py-2 text-sm
                  bg-indigo-600 text-white rounded-md
                  hover:bg-indigo-700">
            + Tambah Pengguna
        </a>
    </div>

    {{-- Search --}}
    <div class="bg-white border rounded-lg p-4">
        <form method="GET"
              class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari username / email ..."
                class="w-full sm:w-72
                       border rounded px-3 py-2
                       text-sm focus:ring focus:ring-indigo-200"
            />

            <select name="role"
                    class="w-full sm:w-auto
                           border rounded px-3 py-2
                           text-sm focus:ring focus:ring-indigo-200">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>
                    Admin
                </option>
                <option value="guru_pembimbing" {{ request('role') == 'guru_pembimbing' ? 'selected' : '' }}>
                    Guru Pembimbing
                </option>
                <option value="pembimbing_lapangan" {{ request('role') == 'pembimbing_lapangan' ? 'selected' : '' }}>
                    Pembimbing Lapangan
                </option>
                <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>
                    Siswa
                </option>
            </select>

            <button type="submit"
                    class="w-full sm:w-auto
                           px-4 py-2 text-sm
                           bg-indigo-600 text-white rounded
                           hover:bg-indigo-700">
                Filter
            </button>

            <a href="{{ route('admin.users.index') }}"
               class="w-full sm:w-auto
                      px-4 py-2 text-sm
                      bg-gray-200 text-gray-700 rounded-md
                      text-sm hover:bg-gray-300">
                Reset
            </a>
        </form>
    </div>

    {{-- TABLE (Desktop ≥ lg) --}}
    <div class="hidden lg:block bg-white border rounded-lg overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3">Username</th>
                    <th class="text-left px-4 py-3">Email</th>
                    <th class="text-left px-4 py-3">Role</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $user->username }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3 capitalize">
                            {{ str_replace('_', ' ', $user->role) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded
                                {{ $user->is_active
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 space-x-3 whitespace-nowrap">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="text-yellow-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ route('admin.users.destroy', $user) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            class="px-4 py-6 text-center text-gray-500">
                            Data pengguna tidak ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CARD VIEW (Mobile & Tablet < lg) --}}
    <div class="lg:hidden space-y-4">
        @forelse ($users as $user)
            <div class="bg-white border rounded-lg p-4 text-sm space-y-2">

                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ $user->username }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $user->email }}
                        </p>
                    </div>

                    <span class="px-2 py-1 text-xs rounded
                        {{ $user->is_active
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <p class="text-gray-600 capitalize">
                    Role: {{ str_replace('_', ' ', $user->role) }}
                </p>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="text-indigo-600 text-sm">
                        Edit
                    </a>

                    <form action="{{ route('admin.users.destroy', $user) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 text-sm">
                            Hapus
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="text-center text-gray-500 py-6">
                Data pengguna tidak ditemukan
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $users->links() }}
    </div>

</div>
</div>
@endsection
