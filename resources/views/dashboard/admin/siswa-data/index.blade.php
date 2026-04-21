@extends('dashboard.admin.layout')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
<div class="flex flex-col gap-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">
            Data Siswa PKL
        </h1>

        <a href="{{ route('admin.siswa.create') }}"
           class="inline-flex items-center justify-center
                  px-4 py-2 text-sm
                  bg-indigo-600 text-white rounded-md
                  hover:bg-indigo-700">
            + Tambah Siswa
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
                placeholder="Cari NIS / Nama Siswa..."
                class="w-full sm:w-72
                       border rounded px-3 py-2
                       text-sm focus:ring focus:ring-indigo-200"
            />

            <button type="submit"
                    class="w-full sm:w-auto
                           px-4 py-2 text-sm
                           bg-indigo-600 text-white rounded
                           hover:bg-indigo-700">
                Cari
            </button>

            <a href="{{ route('admin.siswa.index') }}"
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
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left">NIS</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Kelas</th>
                    <th class="px-4 py-3 text-left">Jurusan</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($siswas as $siswa)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $siswa->nis }}</td>
                        <td class="px-4 py-2">{{ $siswa->nama_lengkap }}</td>
                        <td class="px-4 py-2">{{ $siswa->kelas }}</td>
                        <td class="px-4 py-2">{{ $siswa->jurusan }}</td>
                        <td class="px-4 py-2">{{ $siswa->user->email ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @if ($siswa->user && $siswa->user->is_active)
                                <span class="px-2 py-1 text-xs rounded
                                             bg-green-100 text-green-700">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded
                                             bg-red-100 text-red-700">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.siswa.show', $siswa) }}"
                               class="text-indigo-600 hover:underline">
                                Detail
                            </a>
                            <a href="{{ route('admin.siswa.edit', $siswa) }}"
                               class="text-yellow-600 hover:underline">
                                Edit
                            </a>
                            <form action="{{ route('admin.siswa.destroy', $siswa) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Yakin hapus data siswa ini?')"
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
                            Data siswa belum tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CARD VIEW (Mobile & Tablet < lg) --}}
    <div class="lg:hidden space-y-3">
        @forelse ($siswas as $siswa)
            <div class="bg-white border rounded-lg p-4 text-sm space-y-2">

                <div class="flex justify-between items-start gap-2">
                    <div>
                        <h3 class="font-semibold text-gray-800 leading-tight">
                            {{ $siswa->nama_lengkap }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            NIS: {{ $siswa->nis }}
                        </p>
                    </div>

                    @if ($siswa->user && $siswa->user->is_active)
                        <span class="px-2 py-1 text-xs rounded
                                     bg-green-100 text-green-700">
                            Aktif
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded
                                     bg-red-100 text-red-700">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-gray-600">
                    <div><span class="font-medium">Kelas:</span> {{ $siswa->kelas }}</div>
                    <div><span class="font-medium">Jurusan:</span> {{ $siswa->jurusan }}</div>
                    <div class="sm:col-span-2">
                        <span class="font-medium">Email:</span>
                        {{ $siswa->user->email ?? '-' }}
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('admin.siswa.show', $siswa) }}"
                       class="text-indigo-600 text-sm">
                        Detail
                    </a>
                    <a href="{{ route('admin.siswa.edit', $siswa) }}"
                       class="text-yellow-600 text-sm">
                        Edit
                    </a>
                    <form action="{{ route('admin.siswa.destroy', $siswa) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Yakin hapus data siswa ini?')"
                                class="text-red-600 text-sm">
                            Hapus
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="text-center text-gray-500 py-6">
                Data siswa belum tersedia
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $siswas->links() }}
    </div>

</div>
</div>
@endsection
