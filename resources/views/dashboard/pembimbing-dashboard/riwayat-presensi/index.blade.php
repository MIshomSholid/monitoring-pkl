@extends('dashboard.pembimbing-dashboard.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-4">

            {{-- ================= HEADER ================= --}}
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Riwayat Presensi
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Riwayat presensi siswa PKL yang telah divalidasi oleh Pembimbing Lapangan
                </p>
            </div>

            {{-- ================= FILTER ================= --}}
            <div class="bg-white border rounded-lg p-4">
                <form method="GET" class="flex flex-wrap items-end gap-4 text-sm">

                    {{-- SISWA --}}
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

                    {{-- JENIS --}}
                    <div class="w-full sm:w-40">
                        <label class="block mb-1 font-medium text-gray-700">
                            Jenis
                        </label>
                        <select name="jenis" class="w-full border rounded px-3 py-2 h-10">
                            <option value="">Semua</option>

                            <option value="hadir" {{ request('jenis') == 'hadir' ? 'selected' : '' }}>
                                Hadir
                            </option>

                            <option value="izin" {{ request('jenis') == 'izin' ? 'selected' : '' }}>
                                Izin
                            </option>

                            <option value="libur" {{ request('jenis') == 'libur' ? 'selected' : '' }}>
                                Libur
                            </option>

                            <option value="alpha" {{ request('jenis') == 'alpha' ? 'selected' : '' }}>
                                Alpha
                            </option>
                        </select>
                    </div>

                    {{-- STATUS --}}
                    <div class="w-full sm:w-44">
                        <label class="block mb-1 font-medium text-gray-700">
                            Status
                        </label>
                        <select name="status" class="w-full border rounded px-3 py-2 h-10">
                            <option value="">Semua</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>
                                Diterima
                            </option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                Ditolak
                            </option>
                        </select>
                    </div>

                    {{-- TANGGAL --}}
                    <div class="w-full sm:w-48">
                        <label class="block mb-1 font-medium text-gray-700">
                            Tanggal
                        </label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="w-full border rounded px-3 py-2 h-10">
                    </div>

                    {{-- ACTION --}}
                    <div class="flex gap-2">
                        <button type="submit" class="h-10 px-5 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Filter
                        </button>

                        <a href="{{ route('pembimbing.riwayat-presensi.index') }}"
                            class="h-10 px-5 bg-gray-100 rounded hover:bg-gray-200 flex items-center">
                            Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- ================= DESKTOP TABLE ================= --}}
            <div class="hidden md:block bg-white border rounded-lg">
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Nama Siswa</th>
                            <th class="px-4 py-3 text-left">Jenis</th>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Lokasi Presensi</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Bukti</th>
                        </tr>
                    </thead>

                    <tbody id="presensi-table-body" class="divide-y">
                        @forelse ($data as $item)

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
                                    $badge = 'bg-gray-100 text-gray-600';
                                    $label = ucfirst($item->jenis_presensi);
                                }

                                $statusClass = match ($item->status_validasi) {
                                    'diterima' => 'bg-green-100 text-green-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600'
                                };
                            @endphp

                            <tr class="hover:bg-gray-50 align-top">
                                <td class="px-4 py-2">
                                    {{ $item->tanggal->format('d M Y') }}
                                </td>

                                <td class="px-4 py-2 font-medium">
                                    {{ $item->penempatanPkl->siswa->nama_lengkap }}
                                </td>

                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded {{ $badge }}">
                                        {{ $label }}
                                    </span>
                                </td>

                                {{-- WAKTU --}}
                                <td class="px-4 py-2">
                                    {{ $item->waktu_presensi }}
                                </td>

                                {{-- LOKASI --}}
                                <td class="px-4 py-2 text-xs">
                                    @if($item->jarak_meter !== null)
                                        <div class="text-gray-800">
                                            Jarak: {{ number_format($item->jarak_meter, 2) }} m
                                        </div>
                                        <div class="text-gray-400">
                                            {{ $item->latitude }}, {{ $item->longitude }}
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                        {{ ucfirst($item->status_validasi) }}
                                    </span>

                                    @if($item->status_validasi === 'ditolak' && $item->alasan_penolakan)
                                        <div class="text-xs text-red-600 mt-1">
                                            Alasan: {{ $item->alasan_penolakan }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-2">
                                    @if ($item->jenis_presensi === 'hadir' && $item->foto_presensi)
                                        <a href="{{ $item->foto_presensi }}" target="_blank"
                                            class="text-indigo-600 hover:underline">
                                            Lihat Foto
                                        </a>
                                    @elseif ($item->jenis_presensi === 'izin' && $item->bukti_izin)
                                        <a href="{{ $item->bukti_izin }}" target="_blank"
                                            class="text-indigo-600 hover:underline">
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada riwayat presensi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>



            {{-- ================= MOBILE CARD ================= --}}
            <div class="md:hidden space-y-4">
                @forelse ($data as $item)

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
                            $badge = 'bg-gray-100 text-gray-600';
                            $label = ucfirst($item->jenis_presensi);
                        }

                        $statusClass = match ($item->status_validasi) {
                            'diterima' => 'bg-green-100 text-green-700',
                            'ditolak' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-600'
                        };
                    @endphp

                    <div class="bg-white border rounded-lg p-4 text-sm space-y-2 shadow">
                        <div class="text-gray-600">
                            {{ $item->tanggal->format('d M Y') }}
                        </div>

                        <div class="font-semibold text-gray-800">
                            {{ $item->penempatanPkl->siswa->nama_lengkap }}
                        </div>

                        <div>
                            <span class="px-2 py-1 text-xs rounded {{ $badge }}">
                                {{ $label }}
                            </span>
                        </div>

                        <div>
                            @if ($item->jenis_presensi === 'hadir' && $item->foto_presensi)
                                <a href="{{ $item->foto_presensi }}" target="_blank"
                                    class="text-indigo-600 hover:underline">
                                    Lihat Foto
                                </a>
                            @elseif ($item->jenis_presensi === 'izin' && $item->bukti_izin)
                                <a href="{{ $item->bukti_izin }}" target="_blank"
                                    class="text-indigo-600 hover:underline">
                                    Lihat Bukti
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Waktu</p>
                            <p>{{ $item->waktu_presensi }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Lokasi Presensi</p>

                            @if($item->jarak_meter !== null)
                                <div class="text-sm text-gray-800">
                                    Jarak: {{ number_format($item->jarak_meter, 2) }} m
                                </div>

                                <div class="text-xs text-gray-400 break-all">
                                    {{ $item->latitude }}, {{ $item->longitude }}
                                </div>
                            @else
                                <p>-</p>
                            @endif
                        </div>

                        <div>
                            <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                {{ ucfirst($item->status_validasi) }}
                            </span>

                            @if($item->status_validasi === 'ditolak' && $item->alasan_penolakan)
                                <div class="text-xs text-red-600 mt-2">
                                    Alasan: {{ $item->alasan_penolakan }}
                                </div>
                            @endif
                        </div>
                    </div>

                @empty
                    <div class="bg-white border rounded-lg p-6 text-center text-gray-500 shadow">
                        Belum ada riwayat presensi.
                    </div>
                @endforelse
            </div>

            {{-- ================= PAGINATION ================= --}}
            <div class="mt-6">
                {{ $data->links() }}
            </div>
        </div>
    </div>
@endsection