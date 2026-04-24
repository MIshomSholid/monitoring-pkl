@extends('dashboard.pembimbing-dashboard.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">
    Catatan & Evaluasi Siswa
</h1>

<div class="bg-white p-5 rounded shadow mb-6 text-sm text-gray-700">
    Pembimbing Lapangan dapat memberikan catatan, evaluasi sikap,
    serta saran perbaikan kepada siswa PKL sebagai umpan balik langsung dari industri.
</div>

{{-- ================= INPUT CATATAN ================= --}}
<form method="POST" action="{{ route('pembimbing.evaluasi.store') }}"
      class="bg-white p-6 rounded shadow mb-8 space-y-4">
    @csrf

    <div>
        <label class="font-medium">Siswa PKL</label>
        <select name="penempatan_pkl_id"
                class="w-full border rounded px-3 py-2" required>
            <option value="">Pilih Siswa</option>
            @foreach ($penempatanList as $p)
                <option value="{{ $p->id }}">
                    {{ $p->siswa->nama_lengkap }} — {{ $p->tempat->nama_perusahaan }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="font-medium">Catatan / Evaluasi</label>
        <textarea name="catatan" rows="4"
                  class="w-full border rounded px-3 py-2"
                  placeholder="Tuliskan evaluasi sikap, kinerja, kedisiplinan, atau saran perbaikan..."
                  required></textarea>
    </div>

    <button type="submit"
        class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
        Simpan Catatan
    </button>
</form>

{{-- ================= RIWAYAT ================= --}}
@if ($data->count())
<div class="space-y-4">
    @foreach ($data as $item)
        <div class="bg-white p-5 rounded shadow text-sm">
            <div class="flex justify-between mb-2 text-gray-600">
                <span>
                    {{ $item->penempatan->siswa->nama_lengkap }}
                    — {{ $item->penempatan->tempat->nama_perusahaan }}
                </span>
                <span>
                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                </span>
            </div>

            <div class="text-gray-800 whitespace-pre-line">
                {{ $item->catatan }}
            </div>
        </div>
    @endforeach
</div>

<div class="mt-6">
    {{ $data->links() }}
</div>
@endif
@endsection
