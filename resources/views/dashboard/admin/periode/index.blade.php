@extends('dashboard.admin.layout')

@section('content')

    {{-- TOAST ERROR --}}
    @if(session('error'))
        <div id="error-toast" class="fixed top-6 right-6 z-50 transform translate-x-full
                               bg-white border border-red-200 shadow-xl rounded-xl
                               p-4 w-80 flex items-start gap-3">

            <div class="flex-shrink-0 w-2 h-10 bg-red-500 rounded-full"></div>

            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800">
                    Gagal Menghapus
                </p>

                <p class="text-sm text-gray-600 mt-1">
                    {{ session('error') }}
                </p>
            </div>

            <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">
                ×
            </button>
        </div>
    @endif

    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-4">

            {{-- ================= HEADER ================= --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-2xl font-bold">
                    Periode / Tahun Ajaran PKL
                </h1>

                <a href="{{ route('admin.periode.create') }}" class="inline-flex items-center justify-center
                                      px-4 py-2 text-sm
                                      bg-indigo-600 text-white rounded-md
                                      hover:bg-indigo-700">
                    + Tambah Periode
                </a>
            </div>

            {{-- ================= SEARCH & FILTER ================= --}}
            <div class="bg-white border rounded-lg p-4">
                <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3">

                    {{-- FILTER STATUS --}}
                    <select name="status" class="w-full sm:w-48
                                               border rounded px-3 py-2
                                               text-sm
                                               focus:ring focus:ring-indigo-200">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>

                    <button type="submit" class="px-4 py-2
                                               bg-indigo-600 text-white rounded
                                               hover:bg-indigo-700 text-sm">
                        Filter
                    </button>

                    <a href="{{ route('admin.periode.index') }}" class="w-full sm:w-auto
                                  px-4 py-2 text-sm
                                  bg-gray-200 text-gray-700 rounded-md
                                  text-sm hover:bg-gray-300">
                        Reset
                    </a>
                </form>
            </div>

            {{-- ================= DESKTOP TABLE ================= --}}
            <div class="hidden lg:block bg-white border rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Periode</th>
                            <th class="px-4 py-3 text-left">Tahun Ajaran</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Keterangan</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($periode as $item)
                                        <tr class="hover:bg-gray-50">

                                            <td class="px-4 py-3 font-medium">
                                                {{ $item->nama_periode }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $item->tahun_ajaran }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $item->tanggal_mulai->format('d M Y') }} –
                                                {{ $item->tanggal_selesai->format('d M Y') }}
                                            </td>

                                            <td class="px-4 py-3 text-gray-700">
                                                {{ $item->keterangan ?: '-' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 text-xs rounded
                                                                                                                                {{ $item->is_active
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700' }}">
                                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3 whitespace-nowrap flex items-center gap-3">

                                                <a href="{{ route('admin.periode.edit', $item) }}" class="text-yellow-600 hover:underline">
                                                    Edit
                                                </a>

                                                <a href="{{ route('admin.penempatan.index', ['periode_id' => $item->id]) }}"
                                                    class="text-blue-600 hover:underline">
                                                    Lihat Data
                                                </a>

                                                <form action="{{ route('admin.periode.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus periode ini?')">
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
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada periode PKL
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= MOBILE & TABLET CARD ================= --}}
            <div class="lg:hidden space-y-4">
                @forelse($periode as $item)
                        <div class="bg-white border rounded-lg p-4 text-sm space-y-3">

                            <div>
                                <h3 class="font-semibold text-gray-800">
                                    {{ $item->nama_periode }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    Tahun Ajaran {{ $item->tahun_ajaran }}
                                </p>
                            </div>

                            <div class="text-gray-700">
                                <span class="font-medium">Tanggal:</span><br>
                                {{ $item->tanggal_mulai->format('d M Y') }} –
                                {{ $item->tanggal_selesai->format('d M Y') }}
                            </div>

                            @if($item->keterangan)
                                <div class="text-gray-700">
                                    <span class="font-medium">Keterangan:</span><br>
                                    {{ $item->keterangan }}
                                </div>
                            @endif

                            <span class="inline-block px-2 py-1 text-xs rounded
                                                                            {{ $item->is_active
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700' }}">
                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>

                            <div class="flex gap-4 pt-2">

                                <a href="{{ route('admin.periode.edit', $item) }}" class="text-indigo-600 text-sm">
                                    Edit
                                </a>

                                <a href="{{ route('admin.penempatan.index', ['periode_id' => $item->id]) }}"
                                    class="text-blue-600 text-sm">
                                    Lihat Data
                                </a>

                                <form action="{{ route('admin.periode.destroy', $item) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus periode ini?')">
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
                        Belum ada periode PKL
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <script>
        const toast = document.getElementById('error-toast');

        if (toast) {

            // Slide In
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
                toast.style.transition = "all 0.4s ease";
            }, 100);

            // Auto Close
            setTimeout(closeToast, 5000);

            function closeToast() {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
                toast.style.transition = "all 0.4s ease";

                setTimeout(() => {
                    toast.remove();
                }, 400);
            }
        }
    </script>
@endsection