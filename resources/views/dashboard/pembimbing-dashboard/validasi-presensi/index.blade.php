@extends('dashboard.pembimbing-dashboard.layout')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Validasi Presensi
    </h1>

    <div class="bg-white p-5 rounded-md shadow mb-6 text-sm text-gray-700">
        Halaman ini digunakan untuk memvalidasi presensi harian siswa PKL
        berdasarkan lokasi GPS dan bukti presensi.
    </div>

    {{-- ================= FILTER / SEARCH ================= --}}
    <form method="GET" class="bg-white p-5 rounded-md shadow mb-6">
        <div class="flex flex-col lg:flex-row gap-3 items-start lg:items-end">

            {{-- SEARCH --}}
            <div class="flex flex-col">
                <label class="text-xs text-gray-600 mb-1">Nama Siswa</label>
                <select name="siswa_id" class="w-full min-w-[260px] px-4 py-2 border rounded">
                    <option value="">Semua Siswa</option>
                    @foreach ($daftarSiswa as $siswa)
                        <option value="{{ $siswa->id }}" {{ request('siswa_id') == $siswa->id ? 'selected' : '' }}>
                            {{ $siswa->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TANGGAL DARI --}}
            <div class="flex flex-col">
                <label class="text-xs text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                    class="px-4 py-2 border rounded focus:ring focus:ring-indigo-200">
            </div>

            {{-- TANGGAL SAMPAI --}}
            <div class="flex flex-col">
                <label class="text-xs text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                    class="px-4 py-2 border rounded focus:ring focus:ring-indigo-200">
            </div>

            {{-- BUTTON --}}
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Filter
                </button>

                @if(
                        request()->filled('siswa_id') ||
                        request()->filled('tanggal_dari') ||
                        request()->filled('tanggal_sampai')
                    )
                    <a href="{{ route('pembimbing.validasi-presensi.index') }}"
                        class="px-5 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                        Reset
                    </a>
                @endif
            </div>

        </div>
    </form>


    <div id="presensi-container" class="space-y-6">
        @forelse ($data as $item)
            <div id="presensi-card-{{ $item->id }}" class="bg-white p-6 rounded-md shadow">
                {{-- ================= INFO UTAMA ================= --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 text-sm text-gray-700">
                    <p>
                        <span class="font-medium">Nama Siswa:</span>
                        {{ $item->penempatanPkl->siswa->nama_lengkap }}
                    </p>

                    <p>
                        <span class="font-medium">Tempat PKL:</span>
                        {{ $item->penempatanPkl->tempat->nama_perusahaan ?? '-' }}
                    </p>

                    <p>
                        <span class="font-medium">Jenis Presensi:</span>

                        @php
                            if ($item->jenis_presensi === 'hadir') {
                                if ($item->isTerlambat()) {
                                    $badge = 'bg-orange-100 text-orange-700';
                                    $label = 'Hadir (Terlambat)';
                                } else {
                                    $badge = 'bg-green-100 text-green-700';
                                    $label = 'Hadir';
                                }
                            } elseif ($item->jenis_presensi === 'izin') {
                                $badge = 'bg-yellow-100 text-yellow-700';
                                $label = 'Izin';
                            } elseif ($item->jenis_presensi === 'libur') {
                                $badge = 'bg-slate-200 text-slate-700';
                                $label = 'Libur';
                            } elseif ($item->jenis_presensi === 'alpha') {
                                $badge = 'bg-red-100 text-red-700';
                                $label = 'Alpha';
                            } else {
                                $badge = 'bg-gray-100 text-gray-700';
                                $label = ucfirst($item->jenis_presensi);
                            }
                        @endphp

                        <span class="inline-block px-2 py-1 text-xs rounded {{ $badge }}">
                            {{ $label }}
                        </span>
                    </p>


                    <p>
                        <span class="font-medium">Tanggal:</span>
                        {{ $item->tanggal->format('d M Y') }}
                    </p>

                    <p>
                        <span class="font-medium">Waktu:</span>
                        {{ $item->waktu_presensi }}
                    </p>

                    <p>
                        <span class="font-medium">Jarak:</span>
                        {{ $item->jarak_meter }} meter
                    </p>

                    <p class="md:col-span-2">
                        <span class="font-medium">Koordinat:</span>
                        {{ $item->latitude }}, {{ $item->longitude }}
                    </p>
                </div>

                {{-- ================= BUKTI PRESENSI ================= --}}
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-600 mb-2">
                        Bukti Presensi
                    </p>

                    @if ($item->jenis_presensi === 'hadir' && $item->foto_presensi)
                        <a href="{{ $item->foto_presensi }}" target="_blank">
                            <img src="{{ $item->foto_presensi }}"
                                class="w-48 rounded border hover:opacity-80 transition" alt="Foto Presensi Hadir">
                        </a>

                    @elseif ($item->jenis_presensi === 'izin' && $item->bukti_izin)
                        <a href="{{ $item->bukti_izin }}" target="_blank">
                            <img src="{{ $item->bukti_izin }}"
                                class="w-48 rounded border hover:opacity-80 transition" alt="Bukti Izin">
                        </a>

                    @else
                        <span class="text-sm text-gray-400">
                            Tidak ada bukti presensi
                        </span>
                    @endif
                </div>

                {{-- ================= AKSI VALIDASI ================= --}}
                @if ($item->jenis_presensi !== 'alpha')
                    <div class="border-t pt-4 space-y-4">

                        {{-- TERIMA --}}
                        <form method="POST" action="{{ route('pembimbing.validasi-presensi.terima', $item->id) }}">
                            @csrf
                            <button type="submit"
                                class="w-full md:w-auto px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Terima Presensi
                            </button>
                        </form>

                        {{-- TOLAK --}}
                        <form method="POST" action="{{ route('pembimbing.validasi-presensi.tolak', $item->id) }}" class="space-y-2">
                            @csrf
                            <textarea name="alasan_penolakan" rows="2" placeholder="Tuliskan alasan penolakan presensi..."
                                class="w-full px-3 py-2 border rounded text-sm focus:ring focus:ring-red-200" required></textarea>

                            <button type="submit" class="w-full md:w-auto px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                Tolak Presensi
                            </button>
                        </form>

                    </div>
                @else
                    <div class="border-t pt-4 text-sm text-red-600">
                        Presensi alpha dihasilkan otomatis oleh sistem dan tidak memerlukan validasi.
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white p-6 rounded-md shadow text-center text-gray-500">
                Tidak ada presensi yang menunggu validasi.
            </div>
        @endforelse
    </div>
@endsection