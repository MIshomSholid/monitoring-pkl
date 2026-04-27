@extends('dashboard.pembimbing-dashboard.layout')

@section('content')

    @if(!$isDalamMasaPkl)

        <div class="p-6 bg-gray-50 min-h-screen">
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg">
                <p class="font-semibold">
                    Evaluasi Tidak Tersedia
                </p>
                <p class="text-sm mt-1">
                    Evaluasi hanya dapat dilakukan selama masa PKL.
                </p>
            </div>
        </div>

    @else

        <div class="p-6 bg-gray-50 min-h-screen">

            <h1 class="text-2xl font-bold mb-6">
                Catatan & Evaluasi Siswa
            </h1>

            <div class="bg-white p-5 rounded shadow mb-6 text-sm text-gray-700">
                Pembimbing Lapangan dapat memberikan catatan, evaluasi sikap,
                serta saran perbaikan kepada siswa PKL sebagai umpan balik langsung dari industri.
            </div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('pembimbing.evaluasi.store') }}"
                class="bg-white p-6 rounded shadow mb-8 space-y-4">
                @csrf

                <div>
                    <label class="font-medium">Siswa PKL</label>
                    <select name="penempatan_pkl_id"
                        class="w-full border rounded px-3 py-2" required>

                        <option value="">Pilih Siswa</option>

                        @foreach ($penempatanAktif as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->siswa->nama_lengkap }}
                                — {{ $p->tempat->nama_perusahaan }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div>
                    <label class="font-medium">Catatan / Evaluasi</label>
                    <textarea name="catatan" rows="4"
                        class="w-full border rounded px-3 py-2"
                        placeholder="Tuliskan evaluasi..."
                        required></textarea>
                </div>

                <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Simpan Catatan
                </button>
            </form>

            {{-- RIWAYAT --}}
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

        </div>

    @endif

@endsection