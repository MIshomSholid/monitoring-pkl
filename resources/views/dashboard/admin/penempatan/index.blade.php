@extends('dashboard.admin.layout')

@section('content')
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-6">

            {{-- ================= HEADER ================= --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-xl sm:text-2xl font-bold">
                    Penempatan PKL Siswa
                </h1>

                <a href="{{ route('admin.penempatan.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Tambah Penempatan
                </a>
            </div>

            {{-- ================= SEARCH & FILTER ================= --}}
            <div class="bg-white border rounded-lg p-4">
                @php
                    $periodeAktif = $periode->firstWhere('is_active', true);
                @endphp
                <form method="GET" class="flex flex-col sm:flex-row gap-3">

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari siswa / tempat PKL / guru..."
                        class="w-full sm:w-72 border rounded px-3 py-2 text-sm focus:ring focus:ring-indigo-200" />

                    <select name="periode_id"
                        class="w-full sm:w-56 border rounded px-3 py-2 text-sm focus:ring focus:ring-indigo-200">

                        <option value="all" {{ request('periode_id') == 'all' ? 'selected' : '' }}>
                            Semua Periode
                        </option>

                        @foreach($periode as $p)

                                        <option value="{{ $p->id }}" {{
                            request()->has('periode_id')
                            ? (request('periode_id') == $p->id ? 'selected' : '')
                            : ($periodeAktif && $periodeAktif->id == $p->id ? 'selected' : '')
                                                    }}>
                                            {{ $p->nama_periode }}
                                        </option>

                        @endforeach

                    </select>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
                        Cari
                    </button>

                    <a href="{{ route('admin.penempatan.index') }}"
                        class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Reset
                    </a>
                </form>
            </div>

            {{-- ================= BULK UPDATE ================= --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <form method="POST" action="{{ route('admin.penempatan.bulk-status') }}"
                    class="flex flex-col sm:flex-row gap-3">
                    @csrf

                    <select name="periode_id" required class="w-full sm:w-56 border rounded px-3 py-2 text-sm">
                        <option value="">Pilih Periode</option>
                        @foreach($periode as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_periode }}</option>
                        @endforeach
                    </select>

                    <select name="status" required class="w-full sm:w-48 border rounded px-3 py-2 text-sm">
                        <option value="">Ubah Status ke...</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>

                    <button type="submit" onclick="return confirm('Yakin ingin mengajukan perubahan semua data?')"
                        class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">
                        Ajukan Perubahan
                    </button>
                </form>

                <p class="text-xs text-gray-600 mt-2">
                    ⚠️ Perubahan tidak langsung berlaku, menunggu validasi guru.
                </p>
            </div>

            {{-- ================= TABLE ================= --}}
            <div class="hidden md:block bg-white border rounded-lg">
                <table class="w-full text-sm">
                    <thead class="border-b bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-4">Siswa</th>
                            <th class="text-left py-3 px-4">Tempat PKL</th>
                            <th class="text-left py-3 px-4">Guru</th>
                            <th class="text-left py-3 px-4">Pembimbing</th>
                            <th class="text-left py-3 px-4">Periode</th>
                            <th class="text-left py-3 px-4">Masa PKL</th>
                            <th class="text-left py-3 px-4">Status</th>
                            <th class="text-left py-3 px-4">Pengajuan</th>
                            <th class="text-left py-3 px-4">Validasi</th>
                            <th class="text-left py-3 px-4">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($penempatan as $item)

                            @php
                                $statusClass = $item->status == 'aktif'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700';

                                $pengajuanClass = $item->status_pengajuan == 'aktif'
                                    ? 'bg-green-100 text-green-700'
                                    : ($item->status_pengajuan == 'nonaktif'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-gray-100 text-gray-500');

                                $validasiClass = match ($item->status_validasi) {
                                    'diterima' => 'bg-green-100 text-green-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                    default => 'bg-yellow-100 text-yellow-700'
                                };
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $item->siswa->nama_lengkap }}</td>
                                <td class="px-4 py-3">{{ $item->tempat->nama_perusahaan }}</td>
                                <td class="px-4 py-3">{{ $item->guru->nama_lengkap }}</td>
                                <td class="px-4 py-3">{{ $item->pembimbingLapangan->nama_lengkap }}</td>
                                <td class="px-4 py-3">{{ $item->periode->nama_periode }}</td>

                                <td class="px-4 py-3 text-xs">
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} <br> s/d <br>
                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                </td>

                                {{-- STATUS REAL --}}
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>

                                {{-- STATUS PENGAJUAN --}}
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded {{ $pengajuanClass }}">
                                        {{ ucfirst($item->status_pengajuan ?? '-') }}
                                    </span>
                                </td>

                                {{-- VALIDASI --}}
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded {{ $validasiClass }}">
                                        {{ ucfirst($item->status_validasi) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap space-x-3">
                                    <a href="{{ route('admin.penempatan.edit', $item) }}"
                                        class="text-yellow-600 hover:underline">Edit</a>

                                    <form action="{{ route('admin.penempatan.destroy', $item) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin ingin menghapus penempatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-6 text-center text-gray-500">
                                    Data penempatan PKL belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= MOBILE CARD ================= --}}
            <div class="md:hidden space-y-4">
                @forelse($penempatan as $item)

                    @php
                        $statusClass = $item->status == 'aktif'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700';

                        $pengajuanClass = $item->status_pengajuan == 'aktif'
                            ? 'bg-green-100 text-green-700'
                            : ($item->status_pengajuan == 'nonaktif'
                                ? 'bg-red-100 text-red-700'
                                : 'bg-gray-100 text-gray-500');

                        $validasiClass = match ($item->status_validasi) {
                            'diterima' => 'bg-green-100 text-green-700',
                            'ditolak' => 'bg-red-100 text-red-700',
                            default => 'bg-yellow-100 text-yellow-700'
                        };
                    @endphp

                    <div class="bg-white rounded-lg border shadow-sm p-4">

                        <div class="flex justify-between items-start gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    {{ $item->siswa->nama_lengkap }}
                                </h3>

                                <p class="text-sm text-gray-500">
                                    {{ $item->periode->nama_periode }}
                                </p>
                            </div>

                            <span class="px-2 py-1 rounded text-xs {{ $statusClass }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-2 text-sm">

                            <div class="flex justify-between">
                                <span class="text-gray-500">Tempat PKL</span>
                                <span class="font-medium text-right">
                                    {{ $item->tempat->nama_perusahaan }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500">Guru</span>
                                <span class="text-right">
                                    {{ $item->guru->nama_lengkap }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500">Pembimbing</span>
                                <span class="text-right">
                                    {{ $item->pembimbingLapangan->nama_lengkap }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500">Masa PKL</span>

                                <span class="text-right">
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                    <br>
                                    s/d
                                    <br>
                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                </span>
                            </div>

                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-4">

                            <div>
                                <p class="text-xs text-gray-500 mb-1">
                                    Pengajuan
                                </p>

                                <span class="px-2 py-1 rounded text-xs {{ $pengajuanClass }}">
                                    {{ ucfirst($item->status_pengajuan ?? '-') }}
                                </span>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 mb-1">
                                    Validasi
                                </p>

                                <span class="px-2 py-1 rounded text-xs {{ $validasiClass }}">
                                    {{ ucfirst($item->status_validasi) }}
                                </span>
                            </div>

                        </div>

                        <div class="flex gap-3 mt-5">

                            <a href="{{ route('admin.penempatan.edit', $item) }}"
                                class="flex-1 text-center bg-yellow-500 text-white rounded-md py-2 text-sm hover:bg-yellow-600">
                                Edit
                            </a>

                            <form action="{{ route('admin.penempatan.destroy', $item) }}" method="POST" class="flex-1"
                                onsubmit="return confirm('Yakin ingin menghapus penempatan ini?')">

                                @csrf
                                @method('DELETE')

                                <button class="w-full bg-red-600 text-white rounded-md py-2 text-sm hover:bg-red-700">
                                    Hapus
                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <div class="bg-white rounded-lg border p-8 text-center text-gray-500">
                        Data penempatan PKL belum tersedia
                    </div>

                @endforelse
            </div>

        </div>
    </div>
@endsection