@extends('dashboard.guru-dashboard.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-4">

            {{-- ================= HEADER ================= --}}
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Validasi Laporan Harian
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Daftar laporan kegiatan harian siswa bimbingan
                </p>
            </div>

            {{-- ================= FILTER ================= --}}
            <div class="bg-white border rounded-lg p-4">
                <form method="GET" class="flex flex-wrap items-end gap-4 text-sm">

                    <div class="w-full sm:w-72">
                        <label class="block mb-1 font-medium text-gray-700">
                            Nama Siswa
                        </label>
                        <select name="siswa_id" class="w-full border rounded px-3 py-2 h-10">
                            <option value="">Semua Siswa</option>
                            @foreach ($daftarSiswa as $siswa)
                                <option value="{{ $siswa->id }}" {{ request('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                    {{ $siswa->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-56">
                        <label class="block mb-1 font-medium text-gray-700">
                            Tanggal
                        </label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="w-full border rounded px-3 py-2 h-10">
                    </div>

                    <div class="w-full sm:w-48">
                        <label class="block mb-1 font-medium text-gray-700">
                            Status
                        </label>
                        <select name="status" class="w-full border rounded px-3 py-2 h-10">
                            <option value="">Semua Status</option>
                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="h-10 px-5 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Filter
                        </button>

                        <a href="{{ route('guru.validasi-laporan.index') }}"
                            class="h-10 px-5 bg-gray-100 rounded hover:bg-gray-200 flex items-center">
                            Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- ================= DESKTOP TABLE ================= --}}
            <div class="hidden md:block bg-white border rounded-lg shadow">
                <table class="w-full text-sm table-fixed">
                    <thead class="bg-gray-50 text-gray-700 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Siswa</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Detail</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Validasi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($laporan as $item)

                            @php
                                $statusClass = match ($item->status_validasi) {
                                    'menunggu' => 'bg-yellow-100 text-yellow-700',
                                    'diterima' => 'bg-green-100 text-green-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp

                            <tr id="laporan-{{ $item->id }}" class="hover:bg-gray-50">
                                <td class="px-4 py-2 font-medium">
                                    {{ $item->penempatanPkl->siswa->nama_lengkap }}
                                </td>

                                <td class="px-4 py-2">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                </td>

                                <td class="px-4 py-2">
                                    <a href="{{ route('guru.validasi-laporan.show', $item->id) }}"
                                        class="text-indigo-600 text-xs hover:underline">
                                        Lihat Detail
                                    </a>
                                </td>

                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                        {{ ucfirst($item->status_validasi) }}
                                    </span>
                                </td>

                                {{-- VALIDASI INLINE --}}
                                <td class="px-4 py-3 align-top w-64">
                                    <form method="POST" action="{{ route('guru.validasi-laporan.validasi', $item->id) }}">
                                        @csrf

                                        <select name="status_validasi" class="w-full border rounded px-2 py-1.5 text-xs mb-2">

                                            <option value="menunggu" {{ $item->status_validasi == 'menunggu' ? 'selected' : '' }}>
                                                Menunggu
                                            </option>

                                            <option value="diterima" {{ $item->status_validasi == 'diterima' ? 'selected' : '' }}>
                                                Setujui
                                            </option>

                                            <option value="ditolak" {{ $item->status_validasi == 'ditolak' ? 'selected' : '' }}>
                                                Tolak
                                            </option>

                                        </select>

                                        <textarea name="catatan_validasi"
                                            class="w-full border rounded px-2 py-1.5 text-xs mb-2 resize-none" rows="2"
                                            placeholder="Catatan (opsional)">{{ $item->catatan_validasi }}</textarea>

                                        <button
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs py-1.5 rounded">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada laporan kegiatan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= MOBILE ================= --}}
            <div class="md:hidden space-y-4">
                @forelse ($laporan as $item)

                    @php
                        $statusClass = match ($item->status_validasi) {
                            'diterima' => 'bg-green-100 text-green-700',
                            'ditolak' => 'bg-red-100 text-red-700',
                            default => 'bg-yellow-100 text-yellow-700'
                        };
                    @endphp

                    <div class="bg-white border rounded-lg shadow p-4 text-sm space-y-2">

                        <div class="flex justify-between">
                            <div class="font-semibold">
                                {{ $item->penempatanPkl->siswa->nama_lengkap }}
                            </div>

                            <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                {{ ucfirst($item->status_validasi) }}
                            </span>
                        </div>

                        <div class="text-gray-600">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </div>

                        {{-- VALIDASI --}}
                        <form method="POST" action="{{ route('guru.validasi-laporan.validasi', $item->id) }}">
                            @csrf

                            <select name="status_validasi" class="border rounded px-2 py-1 text-xs mb-1 w-full">
                                <option value="diterima" {{ $item->status_validasi == 'diterima' ? 'selected' : '' }}>
                                    Setujui
                                </option>
                                <option value="ditolak" {{ $item->status_validasi == 'ditolak' ? 'selected' : '' }}>
                                    Tolak
                                </option>
                            </select>

                            <textarea name="catatan_validasi" class="border rounded px-2 py-1 text-xs w-full mb-1" rows="2"
                                placeholder="Catatan">{{ $item->catatan_validasi }}</textarea>

                            <button class="w-full bg-indigo-600 text-white text-xs py-1 rounded">
                                Simpan
                            </button>
                        </form>

                    </div>

                @empty
                    <div class="bg-white border rounded-lg shadow p-6 text-center text-gray-500">
                        Belum ada laporan kegiatan
                    </div>
                @endforelse
            </div>

        </div>
    </div>
@endsection