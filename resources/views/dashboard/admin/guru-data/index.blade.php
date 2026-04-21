@extends('dashboard.admin.layout')

@section('title', 'Data Guru Pembimbing')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
<div class="flex flex-col gap-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
            Data Guru Pembimbing
        </h1>

        <a href="{{ route('admin.guru.create') }}"
           class="inline-flex items-center justify-center px-4 py-2
                  bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
            + Tambah Guru
        </a>
    </div>

    {{-- Search --}}
    <div class="bg-white border rounded-lg p-4">
        <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari NIP / Nama Guru..."
                class="w-full sm:w-72 border rounded px-3 py-2
                       focus:ring focus:ring-indigo-200 text-sm"
            />

            <button type="submit"
                    class="w-full sm:w-auto px-4 py-2
                           bg-indigo-600 text-white rounded
                           hover:bg-indigo-700 text-sm">
                Cari
            </button>

            <a href="{{ route('admin.guru.index') }}"
               class="w-full sm:w-auto
                      px-4 py-2 text-sm
                      bg-gray-200 text-gray-700 rounded-md
                      text-sm hover:bg-gray-300">
                Reset
            </a>
        </form>
    </div>

    {{-- TABLE: Desktop & Laptop --}}
    <div class="hidden lg:block bg-white border rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left">NIP</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Mapel</th>
                    <th class="px-4 py-3 text-left">No Telp</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($guru as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $item->nip }}</td>
                        <td class="px-4 py-2">{{ $item->nama_lengkap }}</td>
                        <td class="px-4 py-2">{{ $item->mata_pelajaran ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $item->no_telepon ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $item->user->email ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @if ($item->user && $item->user->is_active)
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.guru.edit', $item) }}"
                               class="text-yellow-600 hover:underline">
                                Edit
                            </a>
                            <form action="{{ route('admin.guru.destroy', $item) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Yakin hapus data guru ini?')"
                                        class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7"
                            class="px-4 py-6 text-center text-gray-500">
                            Data guru pembimbing belum tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CARD: Mobile & Tablet --}}
    <div class="lg:hidden space-y-3">
        @forelse ($guru as $item)
            <div class="bg-white border rounded-lg p-4 text-sm space-y-2">

                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-gray-800">
                            {{ $item->nama_lengkap }}
                        </h3>
                        <p class="text-gray-500 text-xs">
                            NIP: {{ $item->nip }}
                        </p>
                    </div>

                    @if ($item->user && $item->user->is_active)
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                            Aktif
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div class="text-gray-600 space-y-1">
                    <div><span class="font-medium">Mapel:</span> {{ $item->mata_pelajaran ?? '-' }}</div>
                    <div><span class="font-medium">No Telp:</span> {{ $item->no_telepon ?? '-' }}</div>
                    <div><span class="font-medium">Email:</span> {{ $item->user->email ?? '-' }}</div>
                </div>

                <div class="flex gap-4 pt-2">
                    <a href="{{ route('admin.guru.edit', $item) }}"
                       class="text-yellow-600 text-sm">
                        Edit
                    </a>

                    <form action="{{ route('admin.guru.destroy', $item) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Yakin hapus data guru ini?')"
                                class="text-red-600 text-sm">
                            Hapus
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="text-center text-gray-500 py-6">
                Data guru pembimbing belum tersedia
            </div>
        @endforelse
    </div>

</div>
</div>
@endsection
