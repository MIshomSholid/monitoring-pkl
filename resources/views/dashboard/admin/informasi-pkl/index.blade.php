@extends('dashboard.admin.layout')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
<div class="flex flex-col gap-4">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">
            Informasi PKL
        </h1>

        <a href="{{ route('admin.informasi-pkl.create') }}"
           class="inline-flex items-center justify-center
                  px-4 py-2 text-sm
                  bg-indigo-600 text-white rounded-md
                  hover:bg-indigo-700">
            + Tambah Informasi
        </a>
    </div>

    {{-- ================= DESKTOP TABLE (>= lg) ================= --}}
    <div class="hidden lg:block bg-white border rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left">Judul</th>
                    <th class="px-4 py-3 text-left">Konten</th>
                    <th class="px-4 py-3 text-left">Tipe</th>
                    <th class="px-4 py-3 text-left">Tanggal Publish</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($informasi as $item)
                    <tr class="hover:bg-gray-50">

                        {{-- JUDUL --}}
                        <td class="px-4 py-2 font-medium">
                            {{ $item->judul }}
                        </td>

                        {{-- KONTEN --}}
                        <td class="px-4 py-2 text-gray-700">
                            {{ Str::limit(strip_tags($item->konten), 80) }}
                        </td>

                        {{-- TIPE --}}
                        <td class="px-4 py-2 capitalize">
                            {{ $item->tipe }}
                        </td>

                        {{-- TANGGAL --}}
                        <td class="px-4 py-2">
                            {{ $item->tanggal_publish->format('d M Y') }}
                        </td>

                        {{-- STATUS --}}
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded
                                {{ $item->is_published
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700' }}">
                                {{ $item->is_published ? 'Dipublikasikan' : 'Draft' }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.informasi-pkl.edit', $item) }}"
                               class="text-yellow-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ route('admin.informasi-pkl.destroy', $item) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Hapus informasi ini?')"
                                        class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="px-4 py-6 text-center text-gray-500">
                            Belum ada informasi PKL
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= MOBILE & TABLET CARD (< lg) ================= --}}
    <div class="lg:hidden space-y-3">
        @forelse ($informasi as $item)
            <div class="bg-white border rounded-lg p-4 text-sm space-y-2">

                <div>
                    <h3 class="font-semibold text-gray-800">
                        {{ $item->judul }}
                    </h3>
                    <p class="text-xs text-gray-500">
                        {{ $item->tanggal_publish->format('d M Y') }}
                    </p>
                </div>

                {{-- KONTEN --}}
                <p class="text-gray-700">
                    {{ Str::limit(strip_tags($item->konten), 120) }}
                </p>

                <p class="text-gray-600 capitalize">
                    Tipe: {{ $item->tipe }}
                </p>

                <span class="inline-block px-2 py-1 text-xs rounded
                    {{ $item->is_published
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700' }}">
                    {{ $item->is_published ? 'Dipublikasikan' : 'Draft' }}
                </span>

                <div class="flex gap-4 pt-2">
                    <a href="{{ route('admin.informasi-pkl.edit', $item) }}"
                       class="text-indigo-600 text-sm">
                        Edit
                    </a>

                    <form action="{{ route('admin.informasi-pkl.destroy', $item) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Hapus informasi ini?')"
                                class="text-red-600 text-sm">
                            Hapus
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="text-center text-gray-500 py-6">
                Belum ada informasi PKL
            </div>
        @endforelse
    </div>

</div>
</div>
@endsection
