@extends('dashboard.guru-dashboard.layout')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Detail Laporan Kegiatan
        </h1>
        <p class="text-sm text-gray-600 mt-1">
            Informasi lengkap laporan harian siswa
        </p>
    </div>

    <div class="bg-white rounded-xl shadow p-6 space-y-6">

        {{-- INFO --}}
        @php
            $statusClass = match ($laporan->status_validasi) {
                'menunggu' => 'bg-yellow-100 text-yellow-700',
                'diterima' => 'bg-green-100 text-green-700',
                'ditolak'  => 'bg-red-100 text-red-700',
                default    => 'bg-gray-100 text-gray-700'
            };
        @endphp

        <div class="text-sm space-y-2">
            <p><strong>Nama Siswa:</strong> {{ $laporan->penempatanPkl->siswa->nama_lengkap }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($laporan->tanggal)->format('d M Y') }}</p>

            <p>
                <strong>Status:</strong>
                <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                    {{ ucfirst($laporan->status_validasi) }}
                </span>
            </p>
        </div>

        {{-- DESKRIPSI --}}
        <div>
            <h2 class="font-semibold mb-2">Deskripsi Kegiatan</h2>
            <div class="bg-gray-50 border rounded p-3 text-sm text-gray-700">
                {{ $laporan->deskripsi_kegiatan ?? '-' }}
            </div>
        </div>

        {{-- FILE / FOTO --}}
        <div>
            <h2 class="font-semibold mb-2">Bukti Kegiatan</h2>

            @if ($laporan->file_bukti)
                <div class="space-y-2">

                    {{-- PREVIEW IMAGE --}}
                    <img src="{{ asset('storage/' . $laporan->file_bukti) }}"
                        class="w-64 rounded border shadow cursor-pointer"
                        onclick="window.open(this.src, '_blank')">

                    <a href="{{ asset('storage/' . $laporan->file_bukti) }}"
                       target="_blank"
                       class="text-indigo-600 text-sm hover:underline">
                        Lihat ukuran penuh
                    </a>

                </div>
            @else
                <p class="text-sm text-gray-500">Tidak ada file</p>
            @endif
        </div>

        {{-- ACTION --}}
        <div class="pt-4">
            <a href="{{ route('guru.validasi-laporan.index') }}#laporan-{{ $laporan->id }}"
               class="inline-block px-4 py-2 bg-gray-100 rounded hover:bg-gray-200 text-sm">
                ← Kembali
            </a>
        </div>

    </div>
</div>
@endsection