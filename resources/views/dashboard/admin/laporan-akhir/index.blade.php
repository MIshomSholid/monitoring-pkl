@extends('dashboard.admin.layout')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
<h1 class="text-2xl font-bold mb-4">Laporan Akhir PKL</h1>

<div class="bg-white border rounded-lg p-6 mb-6">

    <p class="text-gray-600 mb-5 text-sm">
        Halaman ini digunakan untuk menghasilkan laporan akhir kegiatan PKL
        berdasarkan periode yang dipilih.
    </p>

    {{-- ================= FILTER ================= --}}
    <form method="POST"
          action="{{ route('admin.laporan-akhir.preview') }}"
          class="max-w-3xl flex flex-col md:flex-row gap-4 mb-6">
        @csrf

        {{-- Periode --}}
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-600 mb-1">
                Periode PKL
            </label>
            <select name="periode_pkl_id"
                    class="w-full border rounded-md px-3 py-2 text-sm"
                    required>
                <option value="">Pilih Periode</option>
                @foreach($periodeList as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->nama_periode }} ({{ $p->tahun_ajaran }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Jenis Laporan --}}
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-600 mb-1">
                Jenis Laporan
            </label>
            <select name="jenis_laporan"
                    class="w-full border rounded-md px-3 py-2 text-sm"
                    required>
                <option value="">Pilih Jenis</option>
                <option value="presensi">Rekap Presensi</option>
                <option value="nilai">Rekap Nilai</option>
                <option value="akhir">Rekap Laporan Akhir</option>
            </select>
        </div>

        {{-- Tombol --}}
        <div class="flex items-end">
            <button
                class="bg-indigo-600 text-white px-5 py-2 rounded-md text-sm hover:bg-indigo-700">
                Tampilkan
            </button>
        </div>
    </form>

    {{-- ================= KETERANGAN ================= --}}
    <div class="bg-gray-50 border rounded-md p-4 text-sm">
        <strong>Keterangan:</strong>
        <ul class="list-disc ml-5 mt-1 text-gray-600 space-y-1">
            <li>Laporan dihasilkan dari presensi, laporan kegiatan, dan KPI.</li>
            <li>Digunakan sebagai dokumentasi resmi PKL.</li>
        </ul>
    </div>

</div>
</div>
@endsection
