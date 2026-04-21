@extends('dashboard.admin.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit Penempatan PKL Siswa</h1>

    <div class="bg-white border rounded-lg p-6 max-w-3xl">
        <form method="POST" action="{{ route('admin.penempatan.update', $penempatan) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- SISWA --}}
                <div>
                    <label class="text-sm font-medium">Siswa</label>
                    <select name="siswa_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($siswa as $item)
                            <option value="{{ $item->id }}" {{ $penempatan->siswa_id == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PERIODE --}}
                <div>
                    <label class="text-sm font-medium">Periode PKL</label>
                    <input type="text" value="{{ $penempatan->periode->nama_periode }}"
                        class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
                </div>

                {{-- TEMPAT --}}
                <div>
                    <label class="text-sm font-medium">Tempat PKL</label>
                    <select name="tempat_pkl_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($tempat as $item)
                            @php
                                $selected = $penempatan->tempat_pkl_id == $item->id;
                            @endphp

                            @if($item->is_full && !$selected)
                                <option disabled class="text-gray-400 bg-gray-100">
                                    {{ $item->nama_perusahaan }} (Full)
                                </option>
                            @else
                                <option value="{{ $item->id }}" {{ $selected ? 'selected' : '' }}>
                                    {{ $item->nama_perusahaan }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- GURU --}}
                <div>
                    <label class="text-sm font-medium">Guru Pembimbing</label>
                    <select name="guru_pembimbing_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($guru as $item)
                            <option value="{{ $item->id }}" {{ $penempatan->guru_pembimbing_id == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PEMBIMBING --}}
                <div class="md:col-span-2">
                    <label class="text-sm font-medium">Pembimbing Lapangan</label>
                    <select name="pembimbing_lapangan_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($pembimbing as $item)
                            <option value="{{ $item->id }}" {{ $penempatan->pembimbing_lapangan_id == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_lengkap }} – {{ $item->jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- STATUS --}}
                <div class="md:col-span-2">
                    <label class="text-sm font-medium">Status Penempatan</label>

                    {{-- Badge status sekarang --}}
                    <div class="mb-2">
                        <span class="px-2 py-1 text-xs rounded
                            {{ $penempatan->status == 'aktif'
        ? 'bg-green-100 text-green-700'
        : 'bg-red-100 text-red-700' }}">
                            Status Saat Ini: {{ ucfirst($penempatan->status) }}
                        </span>
                    </div>

                    <select name="status" class="w-full border rounded px-3 py-2
        {{ ($penempatan->status_pengajuan ?? $penempatan->status) == 'aktif'
        ? 'border-green-400 focus:ring-green-200'
        : 'border-red-400 focus:ring-red-200' }}" required>

                        <option value="aktif" {{ ($penempatan->status_pengajuan ?? $penempatan->status) == 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="nonaktif" {{ ($penempatan->status_pengajuan ?? $penempatan->status) == 'nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>

                    </select>
                </div>

                {{-- TANGGAL --}}
                <div>
                    <label class="text-sm font-medium">Tanggal Mulai PKL</label>
                    <input type="date" name="tanggal_mulai" value="{{ $penempatan->tanggal_mulai }}"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Tanggal Selesai PKL</label>
                    <input type="date" name="tanggal_selesai" value="{{ $penempatan->tanggal_selesai }}"
                        class="w-full border rounded px-3 py-2">
                </div>
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('admin.penempatan.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>

                <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
@endsection