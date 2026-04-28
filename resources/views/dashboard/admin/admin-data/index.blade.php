@extends('dashboard.admin.layout')

@section('title', 'Data Admin')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-4">

            {{-- Header --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                    Data Admin
                </h1>

                <a href="{{ route('admin.admin-data.create') }}" <div class="lg:hidden space-y-3">
                    @forelse ($admins as $admin)
                        <div class="bg-white border rounded-lg p-4 text-sm space-y-2">

                            <div>
                                <h3 class="font-semibold text-gray-800">
                                    {{ $admin->nama_lengkap }}
                                </h3>
                                <p class="text-gray-500 text-xs">
                                    {{ $admin->user->email ?? '-' }}
                                </p>
                            </div>

                            <div class="text-gray-600 space-y-1">
                                <div><span class="font-medium">No Telp:</span> {{ $admin->no_telepon ?? '-' }}</div>
                                <div><span class="font-medium">Alamat:</span> {{ $admin->alamat ?? '-' }}</div>
                            </div>

                            <div class="flex gap-4 pt-2">
                                <a href="{{ route('admin.admin-data.show', $admin->id) }}" class="text-blue-600 text-sm">
                                    Detail
                                </a>

                                <a href="{{ route('admin.admin-data.edit', $admin->id) }}" class="text-yellow-600 text-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.admin-data.destroy', $admin->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Yakin hapus?')" class="text-red-600 text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-6">
                            Data admin belum tersedia
                        </div>
                    @endforelse
            </div>
            class="inline-flex items-center justify-center px-4 py-2
            bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
            + Tambah Admin
            </a>
        </div>

        {{-- Search --}}
        <div class="bg-white border rounded-lg p-4">
            <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / Email / No HP..."
                    class="w-full sm:w-72 border rounded px-3 py-2
                               focus:ring focus:ring-indigo-200 text-sm" />

                <button type="submit" class="w-full sm:w-auto px-4 py-2
                                   bg-indigo-600 text-white rounded
                                   hover:bg-indigo-700 text-sm">
                    Cari
                </button>

                <a href="{{ route('admin.admin-data.index') }}" class="w-full sm:w-auto
                              px-4 py-2 text-sm
                              bg-gray-200 text-gray-700 rounded-md
                              hover:bg-gray-300">
                    Reset
                </a>
            </form>
        </div>

        {{-- TABLE DESKTOP --}}
        <div class="hidden lg:block bg-white border rounded-lg overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">No Telp</th>
                        <th class="px-4 py-3 text-left">Alamat</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($admins as $admin)
                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-2">{{ $admin->nama_lengkap }}</td>

                            <td class="px-4 py-2">
                                {{ $admin->user->email ?? '-' }}
                            </td>

                            <td class="px-4 py-2">
                                {{ $admin->no_telepon ?? '-' }}
                            </td>

                            <td class="px-4 py-2">
                                {{ $admin->alamat ?? '-' }}
                            </td>

                            <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.admin-data.show', $admin->id) }}"
                                    class="text-blue-600 hover:underline">
                                    Detail
                                </a>

                                <a href="{{ route('admin.admin-data.edit', $admin->id) }}"
                                    class="text-yellow-600 hover:underline">
                                    Edit
                                </a>

                                <form action="{{ route('admin.admin-data.destroy', $admin->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Yakin hapus?')" class="text-red-600 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                Data admin belum tersedia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD MOBILE --}}
        <div class="lg:hidden space-y-3">
            @forelse ($admins as $admin)
                <div class="bg-white border rounded-lg p-4 text-sm space-y-2">

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            {{ $admin->nama_lengkap }}
                        </h3>
                        <p class="text-gray-500 text-xs">
                            {{ $admin->user->email ?? '-' }}
                        </p>
                    </div>

                    <div class="text-gray-600 space-y-1">
                        <div><span class="font-medium">No Telp:</span> {{ $admin->no_telepon ?? '-' }}</div>
                        <div><span class="font-medium">Alamat:</span> {{ $admin->alamat ?? '-' }}</div>
                    </div>

                    <div class="flex gap-4 pt-2">
                        <a href="{{ route('admin.admin-data.show', $admin->id) }}" class="text-blue-600 text-sm">
                            Detail
                        </a>

                        <a href="{{ route('admin.admin-data.edit', $admin->id) }}" class="text-yellow-600 text-sm">
                            Edit
                        </a>

                        <form action="{{ route('admin.admin-data.destroy', $admin->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Yakin hapus?')" class="text-red-600 text-sm">
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
            @empty
                <div class="text-center text-gray-500 py-6">
                    Data admin belum tersedia
                </div>
            @endforelse
        </div>

    </div>
    </div>
@endsection