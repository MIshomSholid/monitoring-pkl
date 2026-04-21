@extends('dashboard.siswa-dashboard.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Laporan Kegiatan Harian PKL
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Isi laporan kegiatan harian setelah melakukan presensi
            </p>
        </div>

        {{-- PESAN STATUS PRESENSI --}}
        @if (!$bolehInput && $pesan)
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md text-yellow-700">
                <p class="font-semibold text-base">
                    {{ $pesan }}
                </p>
            </div>
        @endif

        @if ($bolehInput && !$sudahLapor)
            {{-- FORM INPUT --}}
            <div class="bg-white rounded-xl shadow p-6 mb-8">
                <form method="POST" action="{{ route('siswa.laporan-kegiatan.store') }}" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Tanggal Kegiatan
                        </label>
                        <input type="date" value="{{ now()->toDateString() }}"
                            class="w-full mt-1 rounded-md border-gray-300 bg-gray-100" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Deskripsi Kegiatan
                        </label>
                        <textarea name="deskripsi_kegiatan" rows="4" class="w-full mt-1 rounded-md border-gray-300"
                            required></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            File Bukti Kegiatan (opsional)
                        </label>
                        <input type="file" name="file_bukti" class="mt-1 block w-full text-sm">
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Simpan Laporan
                    </button>
                </form>
            </div>
        @elseif ($bolehInput && $sudahLapor)
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md text-green-700 font-bold">
                Laporan kegiatan hari ini sudah dikirim.
            </div>
        @endif

    </div>
@endsection