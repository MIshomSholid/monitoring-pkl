@extends('dashboard.siswa-dashboard.layout')

@section('content')
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                Riwayat Presensi dan Izin
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Halaman ini menampilkan riwayat kehadiran dan izin siswa selama PKL
            </p>
        </div>

        {{-- ================= DESKTOP TABLE ================= --}}
        <div class="hidden lg:block bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full text-sm" data-realtime="riwayat-presensi">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Lokasi Presensi</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Bukti</th>
                    </tr>
                </thead>
                <tbody id="presensi-table-body">
                    @forelse ($presensi as $item)
                        <tr class="border-b hover:bg-gray-50">

                            {{-- TANGGAL --}}
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            </td>

                            {{-- JENIS --}}
                            <td class="px-4 py-3">
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
                                @endphp
                                <span class="px-2 py-1 text-xs rounded {{ $badge }}">
                                    {{ $label }}
                                </span>
                            </td>

                            {{-- WAKTU --}}
                            <td class="px-4 py-3">
                                @if($item->jenis_presensi === 'alpha' || empty($item->waktu_presensi))
                                    -
                                @else
                                    {{ $item->waktu_presensi }}
                                @endif
                            </td>

                            {{-- LOKASI --}}
                            <td class="px-4 py-3 text-xs">
                                @if ($item->jenis_presensi === 'hadir')
                                    Jarak: {{ $item->jarak_meter }} m <br>
                                    <span class="text-gray-400">
                                        {{ $item->latitude }}, {{ $item->longitude }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-3">
                                @php
                                    $status = $item->status_validasi ?? 'menunggu';
                                @endphp

                                <span class="px-2 py-1 text-xs rounded
                                                                                    @if($status === 'diterima')
                                                                                        bg-green-100 text-green-700
                                                                                    @elseif($status === 'ditolak')
                                                                                        bg-red-100 text-red-700
                                                                                    @else
                                                                                        bg-blue-100 text-blue-700
                                                                                    @endif
                                                                                ">
                                    {{ ucfirst($status) }}
                                </span>

                                @if($status === 'ditolak' && $item->alasan_penolakan)
                                    <div class="text-xs text-red-600 mt-1">
                                        Alasan: {{ $item->alasan_penolakan }}
                                    </div>
                                @endif
                            </td>

                            {{-- BUKTI --}}
                            <td class="px-4 py-3 text-xs">
                                @if ($item->jenis_presensi === 'hadir' && $item->foto_presensi)
                                    <a href="{{ asset('storage/' . $item->foto_presensi) }}" target="_blank"
                                        class="text-blue-600 hover:underline">
                                        Lihat Foto
                                    </a>
                                @elseif ($item->jenis_presensi === 'izin' && $item->bukti_izin)
                                    <a href="{{ asset('storage/' . $item->bukti_izin) }}" target="_blank"
                                        class="text-blue-600 hover:underline">
                                        Lihat Bukti
                                    </a>
                                @else
                                    -
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                Belum terdapat data presensi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $presensi->links() }}
            </div>
        </div>

        {{-- ================= MOBILE CARD ================= --}}
        <div class="space-y-4 lg:hidden">
            @forelse ($presensi as $item)

                <div class="bg-white rounded-xl shadow p-4 space-y-3">

                    {{-- TANGGAL + STATUS --}}
                    <div class="flex justify-between items-start">
                        <div class="font-semibold text-gray-800 text-sm">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </div>

                        @php $status = $item->status_validasi ?? 'menunggu'; @endphp
                        <span class="px-2 py-1 text-xs rounded
                                    @if($status === 'diterima')
                                        bg-green-100 text-green-700
                                    @elseif($status === 'ditolak')
                                        bg-red-100 text-red-700
                                    @else
                                        bg-blue-100 text-blue-700
                                    @endif
                                ">
                            {{ ucfirst($status) }}
                        </span>
                    </div>

                    {{-- JENIS --}}
                    <div>
                        @php
                            if ($item->jenis_presensi === 'hadir') {
                                $badge = $item->isTerlambat()
                                    ? 'bg-orange-100 text-orange-700'
                                    : 'bg-green-100 text-green-700';
                                $label = $item->isTerlambat() ? 'Hadir (Terlambat)' : 'Hadir';
                            } elseif ($item->jenis_presensi === 'izin') {
                                $badge = 'bg-yellow-100 text-yellow-700';
                                $label = 'Izin';
                            } elseif ($item->jenis_presensi === 'libur') {
                                $badge = 'bg-slate-200 text-slate-700';
                                $label = 'Libur';
                            } elseif ($item->jenis_presensi === 'alpha') {
                                $badge = 'bg-red-100 text-red-700';
                                $label = 'Alpha';
                            }
                        @endphp

                        <span class="px-2 py-1 text-xs rounded {{ $badge }}">
                            {{ $label }}
                        </span>
                    </div>

                    {{-- WAKTU --}}
                    <div>
                        <p class="text-xs text-gray-500">Waktu</p>
                        <p class="text-sm text-gray-800">
                            @if($item->jenis_presensi === 'alpha' || empty($item->waktu_presensi))
                                -
                            @else
                                {{ $item->waktu_presensi }}
                            @endif
                        </p>
                    </div>

                    {{-- LOKASI --}}
                    @if ($item->jenis_presensi === 'hadir')
                        <div>
                            <p class="text-xs text-gray-500">Lokasi Presensi</p>
                            <div class="text-sm text-gray-800">
                                Jarak: {{ number_format($item->jarak_meter, 2) }} m
                            </div>
                            <div class="text-xs text-gray-400 break-all">
                                {{ $item->latitude }}, {{ $item->longitude }}
                            </div>
                        </div>
                    @endif

                    {{-- BUKTI --}}
                    <div>
                        @if ($item->jenis_presensi === 'hadir' && $item->foto_presensi)
                            <a href="{{ asset('storage/' . $item->foto_presensi) }}" target="_blank"
                                class="text-blue-600 hover:underline text-sm">
                                Lihat Foto
                            </a>
                        @elseif ($item->jenis_presensi === 'izin' && $item->bukti_izin)
                            <a href="{{ asset('storage/' . $item->bukti_izin) }}" target="_blank"
                                class="text-blue-600 hover:underline text-sm">
                                Lihat Bukti
                            </a>
                        @endif
                    </div>

                    {{-- ALASAN --}}
                    @if($status === 'ditolak' && $item->alasan_penolakan)
                        <div class="text-xs text-red-600">
                            Alasan: {{ $item->alasan_penolakan }}
                        </div>
                    @endif

                </div>

            @empty
                <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
                    Belum terdapat data presensi
                </div>
            @endforelse
        </div>
@endsection