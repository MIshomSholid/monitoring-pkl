@extends('dashboard.admin.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Tambah Penempatan PKL</h1>

    <div class="bg-white border rounded-lg p-6 max-w-3xl">
        <form method="POST" action="{{ route('admin.penempatan.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- SISWA --}}
                <div>
                    <label class="text-sm font-medium">Siswa</label>
                    <select name="siswa_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Siswa --</option>

                        @forelse($siswa as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->nama_lengkap }}
                            </option>
                        @empty
                            <option value="" disabled>
                                Semua siswa sudah ditempatkan
                            </option>
                        @endforelse
                    </select>
                </div>

                {{-- PERIODE --}}
                <div>
                    <label class="text-sm font-medium">Periode PKL</label>
                    <select name="periode_pkl_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($periode as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->nama_periode }} ({{ $item->tahun_ajaran }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TEMPAT PKL --}}
                <div>
                    <label class="text-sm font-medium">Tempat PKL</label>
                    <select name="tempat_pkl_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($tempat as $item)
                            @if($item->is_full)
                                <option disabled class="text-gray-400 bg-gray-100">
                                    {{ $item->nama_perusahaan }} (Full)
                                </option>
                            @else
                                <option value="{{ $item->id }}">
                                    {{ $item->nama_perusahaan }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- GURU PEMBIMBING --}}
                <div>
                    <label class="text-sm font-medium">Guru Pembimbing</label>
                    <select name="guru_pembimbing_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($guru as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PEMBIMBING LAPANGAN --}}
                <div class="md:col-span-2">
                    <label class="text-sm font-medium">Pembimbing Lapangan</label>
                    <select name="pembimbing_lapangan_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($pembimbing as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->nama_lengkap }} - {{ $item->jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- TANGGAL MULAI --}}
            <div>
                <label class="text-sm font-medium">Tanggal Mulai PKL</label>
                <input type="date" name="tanggal_mulai" class="w-full border rounded px-3 py-2" required>
            </div>

            {{-- TANGGAL SELESAI --}}
            <div>
                <label class="text-sm font-medium">Tanggal Selesai PKL</label>
                <input type="date" name="tanggal_selesai" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('admin.penempatan.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">
                    Simpan Penempatan
                </button>
            </div>

        </form>
    </div>
@endsection