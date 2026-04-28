@extends('dashboard.admin.layout')

@section('content')

<div class="p-6 bg-gray-50 min-h-screen">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">
            Data Admin
        </h1>

        <a href="{{ route('admin.admin-data.create') }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            + Tambah Admin
        </a>
    </div>

    {{-- SEARCH --}}
    <form method="GET" class="bg-white p-4 rounded shadow mb-6 flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari Nama / Email / No HP..."
            class="border px-3 py-2 rounded w-80">

        <button class="px-4 py-2 bg-indigo-600 text-white rounded">
            Cari
        </button>

        <a href="{{ route('admin.admin-data.index') }}"
           class="px-4 py-2 bg-gray-300 rounded">
            Reset
        </a>
    </form>

    {{-- TABLE --}}
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr class="text-left">
                    <th class="p-3">No</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">No Telepon</th>
                    <th class="p-3">Alamat</th>
                    <th class="p-3">Foto</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($admins as $index => $admin)
                    <tr class="border-t">
                        <td class="p-3">{{ $loop->iteration }}</td>

                        <td class="p-3">
                            {{ $admin->nama_lengkap }}
                        </td>

                        <td class="p-3">
                            {{ $admin->user->email ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $admin->no_telepon ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $admin->alamat ?? '-' }}
                        </td>

                        <td class="p-3">
                            @if($admin->foto_profil)
                                <img src="{{ asset('storage/' . $admin->foto_profil) }}"
                                     class="w-10 h-10 rounded object-cover">
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        <td class="p-3 space-x-2">
                            <a href="{{ route('admin.admin-data.show', $admin->id) }}"
                               class="text-blue-600 hover:underline">
                                Detail
                            </a>

                            <a href="{{ route('admin.admin-data.edit', $admin->id) }}"
                               class="text-yellow-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ route('admin.admin-data.destroy', $admin->id) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Yakin hapus?')"
                                        class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-5 text-center text-gray-500">
                            Data admin belum tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection