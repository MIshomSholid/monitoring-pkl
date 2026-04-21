@extends('dashboard.siswa-dashboard.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">
    Catatan & Evaluasi
</h1>

<div class="bg-white p-5 rounded shadow mb-6 text-sm text-gray-700">
    Halaman ini menampilkan catatan dan evaluasi dari Pembimbing Lapangan sebagai
    bahan pembelajaran dan peningkatan kinerja selama PKL.
</div>

@if ($evaluasi->count())

    <div class="space-y-6">

        @foreach ($evaluasi as $item)
            <div class="bg-white p-6 rounded shadow border-l-4 border-indigo-500">

                <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-3">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-gray-800">
                            {{ $item->creator->name ?? 'Pembimbing Lapangan' }}
                        </span>
                        • {{ $item->tanggal->translatedFormat('d F Y') }}
                    </div>

                    <span class="text-xs px-3 py-1 rounded bg-indigo-100 text-indigo-700">
                        Evaluasi PKL
                    </span>
                </div>

                <div class="text-gray-800 leading-relaxed whitespace-pre-line">
                    {{ $item->catatan }}
                </div>

            </div>
        @endforeach

    </div>

@else
    <div class="bg-white p-6 rounded shadow text-center text-gray-500">
        Belum ada catatan atau evaluasi dari Pembimbing Lapangan.
    </div>
@endif

@endsection
