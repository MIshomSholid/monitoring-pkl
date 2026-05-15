@extends('dashboard.admin.layout')

@section('content')

    {{-- TOAST SUCCESS --}}
    @if(session('success'))
        <div id="toast-success" class="fixed top-6 right-6 z-50 translate-x-full
                    bg-white border border-green-200 shadow-xl rounded-xl
                    p-4 w-80 flex items-start gap-3 transition-all duration-500">

            <div class="text-green-600 text-xl">✔</div>

            <div class="flex-1">
                <h4 class="font-semibold text-gray-800">
                    Berhasil
                </h4>

                <p class="text-sm text-gray-600">
                    {{ session('success') }}
                </p>
            </div>

            <button onclick="closeToast('toast-success')" class="text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </div>
    @endif

    {{-- TOAST ERROR --}}
    @if(session('error'))
        <div id="toast-error" class="fixed top-6 right-6 z-50 translate-x-full
                    bg-white border border-red-200 shadow-xl rounded-xl
                    p-4 w-80 flex items-start gap-3 transition-all duration-500">

            <div class="text-red-600 text-xl">⚠</div>

            <div class="flex-1">
                <h4 class="font-semibold text-gray-800">
                    Gagal
                </h4>

                <p class="text-sm text-gray-600">
                    {{ session('error') }}
                </p>
            </div>

            <button onclick="closeToast('toast-error')" class="text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </div>
    @endif

    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-6">

            {{-- HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-xl sm:text-2xl font-bold">Manajemen Tempat PKL</h1>

                <a href="{{ route('admin.tempat-pkl.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Tambah Tempat
                </a>
            </div>

            {{-- SEARCH --}}
            <div class="bg-white border rounded-lg p-4">
                <form method="GET" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama tempat / alamat / email..."
                        class="w-full sm:w-72 border rounded px-3 py-2 text-sm focus:ring focus:ring-indigo-200" />

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
                        Cari
                    </button>

                    <a href="{{ route('admin.tempat-pkl.index') }}"
                        class="w-full sm:w-auto px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Reset
                    </a>
                </form>
            </div>

            {{-- ================= DESKTOP TABLE ================= --}}
            <div class="hidden md:block bg-white border rounded-lg">
                <table class="w-full text-sm">
                    <thead class="border-b bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-4">Nama Tempat</th>
                            <th class="text-left py-3 px-4">Alamat</th>
                            <th class="text-left py-3 px-4">Radius</th>
                            <th class="text-left py-3 px-4">Kuota</th>
                            <th class="text-left py-3 px-4">Jam Masuk</th>
                            <th class="text-left py-3 px-4">Hari Wajib</th>
                            <th class="text-left py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($tempat as $item)
                            @php
                                // Hitung manual agar akurat, tidak bergantung pada method sisaKuota() yang mungkin error
                                $terpakai = (int) $item->terpakai(); // pastikan integer
                                $kuota = (int) $item->kuota_siswa;
                                $sisa = $kuota - $terpakai; // biarkan bisa negatif jika over kuota
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">
                                    {{ $item->nama_perusahaan }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $item->alamat }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $item->radius_meter }} m
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">
                                        {{ $terpakai }} / {{ $kuota }}
                                    </div>

                                    <div class="text-xs {{ $sisa <= 0 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                        Sisa: {{ $sisa }}
                                    </div>

                                    @if($sisa <= 0)
                                        <span class="text-xs text-red-600 px-2 py-1 rounded">
                                            Full
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    {{ $item->jam_masuk ?? '-' }}
                                    <div class="text-xs text-gray-500">
                                        Tol: {{ $item->toleransi_keterlambatan }} mnt
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    {{ $item->hari_wajib ? implode(', ', $item->hari_wajib) : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap space-x-3">
                                    <a href="{{ route('admin.tempat-pkl.edit', $item) }}"
                                        class="text-yellow-600 hover:underline">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.tempat-pkl.destroy', $item) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus tempat PKL ini?')">
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
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    Data tempat PKL belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= MOBILE CARD ================= --}}
            <div class="space-y-4 md:hidden">
                @forelse($tempat as $item)
                    @php
                        $terpakai = (int) $item->terpakai();
                        $kuota = (int) $item->kuota_siswa;
                        $sisa = $kuota - $terpakai;
                    @endphp
                    <div class="bg-white border rounded-lg p-4 shadow-sm space-y-3">
                        <div>
                            <h2 class="font-semibold text-gray-800">
                                {{ $item->nama_perusahaan }}
                            </h2>
                            <p class="text-xs text-gray-500">
                                {{ $item->alamat }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs">Radius</p>
                                <p>{{ $item->radius_meter }} m</p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">Kuota</p>
                                <div class="flex flex-col gap-1">
                                    <div class="font-semibold text-gray-800">
                                        {{ $terpakai }} / {{ $kuota }}
                                    </div>

                                    @if($sisa <= 0)
                                        <span
                                            class="inline-block text-xs bg-red-100 text-red-600 px-2 py-1 rounded font-medium w-fit">
                                            FULL
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500">
                                            Sisa: {{ $sisa }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">Jam Masuk</p>
                                <p>{{ $item->jam_masuk ?? '-' }}</p>
                                <p class="text-xs text-gray-400">
                                    Tol: {{ $item->toleransi_keterlambatan }} mnt
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">Hari Wajib</p>
                                <p class="text-xs">
                                    {{ $item->hari_wajib ? implode(', ', $item->hari_wajib) : '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-2 border-t text-sm">
                            <a href="{{ route('admin.tempat-pkl.edit', $item) }}" class="text-yellow-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ route('admin.tempat-pkl.destroy', $item) }}" method="POST"
                                onsubmit="return confirm('Hapus tempat PKL ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border rounded-lg p-6 text-center text-gray-500">
                        Data tempat PKL belum tersedia
                    </div>
                @endforelse
            </div>

        </div>
    </div>
    <script>
        function showToast(id) {
            const toast = document.getElementById(id);

            if (toast) {
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);

                setTimeout(() => {
                    closeToast(id);
                }, 5000);
            }
        }

        function closeToast(id) {
            const toast = document.getElementById(id);

            if (toast) {
                toast.classList.add('translate-x-full');
            }
        }

        showToast('toast-success');
        showToast('toast-error');
    </script>

@endsection