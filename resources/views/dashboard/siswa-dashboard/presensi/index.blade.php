@extends('dashboard.siswa-dashboard.layout')

@section('content')

    @if(!$presensiAktif)
        <div class="p-6 bg-gray-50 min-h-screen">
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg">
                <p class="font-semibold">
                    Presensi Tidak Tersedia
                </p>
                <p class="text-sm mt-1">
                    {{ $alasan }}
                </p>
            </div>
        </div>

    @else
        <div class="p-6 bg-gray-50 min-h-screen">
            <h1 class="text-2xl font-bold mb-2">Presensi Harian</h1>
            <p class="text-gray-600 mb-6">
                Silakan lakukan presensi sesuai kondisi Anda hari ini.
            </p>

            {{-- ================= STATUS HARI INI ================= --}}
            @if ($presensiHariIni)

                @php
                    $warna = match ($presensiHariIni->jenis_presensi) {
                        'hadir' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                        'izin' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
                        'libur' => 'bg-gray-50 border-gray-200 text-gray-700',
                        'alpha' => 'bg-red-50 border-red-200 text-red-700',
                        default => 'bg-blue-50 border-blue-200 text-blue-700',
                    };
                @endphp

                <div class="border p-4 rounded-lg {{ $warna }}">
                    <p class="font-semibold">
                        Presensi hari ini telah tercatat.
                    </p>

                    <p class="text-sm mt-1">
                        Jenis presensi:
                        <strong>{{ strtoupper($presensiHariIni->jenis_presensi) }}</strong>
                    </p>
                </div>

            @else

                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-amber-800">
                        Pemberitahuan Presensi
                    </h2>

                    <div class="mt-3 text-sm text-amber-700 leading-relaxed">
                        <p>
                            Anda belum melakukan presensi untuk hari ini.
                        </p>
                        <p class="mt-2 text-amber-600">
                            Silakan memilih jenis presensi di bawah ini sebelum batas waktu yang telah ditetapkan.
                        </p>
                    </div>
                </div>

                {{-- ================= PILIH JENIS PRESENSI ================= --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl">

                    {{-- KARTU PRESENSI HADIR --}}
                    <div class="bg-white border rounded-lg p-6 shadow-sm hover:shadow transition">
                        <h2 class="text-lg font-semibold mb-2">Presensi Hadir</h2>

                        <p class="text-sm text-gray-600 mb-4">
                            Digunakan jika Anda hadir di lokasi PKL.
                            Sistem akan memverifikasi lokasi dan mengambil foto sebagai bukti kehadiran.
                        </p>

                        <ul class="text-sm text-gray-500 space-y-1 mb-5">
                            <li>• Verifikasi lokasi (radius PKL)</li>
                            <li>• Pengambilan foto melalui kamera</li>
                            <li>• Status menunggu validasi</li>
                        </ul>

                        <button type="button" onclick="showHadir()"
                            class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                            Lakukan Presensi Hadir
                        </button>
                    </div>

                    {{-- KARTU PRESENSI IZIN --}}
                    <div class="bg-white border rounded-lg p-6 shadow-sm hover:shadow transition">
                        <h2 class="text-lg font-semibold mb-2">Presensi Izin</h2>

                        <p class="text-sm text-gray-600 mb-4">
                            Digunakan jika Anda tidak dapat hadir di lokasi PKL.
                            Wajib mengisi keterangan dan dapat melampirkan bukti pendukung.
                        </p>

                        <ul class="text-sm text-gray-500 space-y-1 mb-5">
                            <li>• Alasan izin wajib diisi</li>
                            <li>• Upload bukti (opsional)</li>
                            <li>• Status menunggu validasi</li>
                        </ul>

                        <button type="button" onclick="showIzin()"
                            class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                            Ajukan Presensi Izin
                        </button>
                    </div>

                    {{-- KARTU PRESENSI LIBUR --}}
                    <div class="bg-white border rounded-lg p-6 shadow-sm hover:shadow transition">
                        <h2 class="text-lg font-semibold mb-2">Presensi Libur</h2>

                        <p class="text-sm text-gray-600 mb-4">
                            Digunakan jika hari ini merupakan hari libur / tanggal merah.
                            Presensi akan dicatat sebagai libur.
                        </p>

                        <ul class="text-sm text-gray-500 space-y-1 mb-5">
                            <li>• Untuk hari libur resmi / tanggal merah</li>
                            <li>• Tidak perlu foto kehadiran</li>
                            <li>• Status menunggu validasi</li>
                        </ul>

                        <button type="button" onclick="showLibur()"
                            class="w-full px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                            Ajukan Presensi Libur
                        </button>
                    </div>

                </div>

                {{-- ================= KONTEN DINAMIS ================= --}}
                <div id="hadirBox" class="hidden mt-8">
                    @include('dashboard.siswa-dashboard.presensi.hadir')
                </div>

                <div id="izinBox" class="hidden mt-8">
                    @include('dashboard.siswa-dashboard.presensi.izin')
                </div>

                <div id="liburBox" class="hidden mt-8">
                    @include('dashboard.siswa-dashboard.presensi.libur')
                </div>

            @endif

        </div>

    @endif

    {{-- ================= SCRIPT GLOBAL ================= --}}
    <script>
        const hadirBox = document.getElementById('hadirBox');
        const izinBox = document.getElementById('izinBox');
        const liburBox = document.getElementById('liburBox');

        window.showHadir = function () {
            hadirBox.classList.remove('hidden');
            izinBox.classList.add('hidden');
            liburBox.classList.add('hidden');

            setTimeout(() => {
                if (window.startPresensiHadir) {
                    window.startPresensiHadir();
                }
            }, 0);
        };

        window.showIzin = function () {
            izinBox.classList.remove('hidden');
            hadirBox.classList.add('hidden');
            liburBox.classList.add('hidden');

            if (window.stopPresensiHadir) {
                window.stopPresensiHadir();
            }
        };

        window.showLibur = function () {
            liburBox.classList.remove('hidden');
            hadirBox.classList.add('hidden');
            izinBox.classList.add('hidden');

            if (window.stopPresensiHadir) {
                window.stopPresensiHadir();
            }
        };

        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', function () {
                showIzin();
            });
        @endif
    </script>

@endsection