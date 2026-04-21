@extends('dashboard.admin.layout')

@section('content')

    <h1 class="text-2xl font-bold mb-6">Tambah Periode PKL</h1>

    {{-- ================= TOAST ERROR ================= --}}
    @if ($errors->has('tanggal_mulai'))
        <div id="toast-error" class="fixed top-6 right-6 z-50
                                    bg-red-500 text-white px-5 py-3 rounded-lg shadow-lg
                                    animate-slide-in">
            {{ $errors->first('tanggal_mulai') }}
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-error');
                if (toast) toast.remove();
            }, 4000);
        </script>
    @endif


    <div class="bg-white border rounded-lg p-6 max-w-xl">
        <form method="POST" action="{{ route('admin.periode.store') }}">
            @csrf

            {{-- Nama Periode --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Nama Periode
                </label>

                <input type="text" name="nama_periode" value="{{ old('nama_periode') }}" class="w-full border rounded px-3 py-2
                                      @error('nama_periode') border-red-500 @enderror" required>

                @error('nama_periode')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Tahun Ajaran --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Tahun Ajaran
                </label>

                <input type="text" name="tahun_ajaran" placeholder="2026/2027" value="{{ old('tahun_ajaran') }}" class="w-full border rounded px-3 py-2
                                      @error('tahun_ajaran') border-red-500 @enderror" pattern="\d{4}/\d{4}"
                    title="Format harus seperti 2026/2027" required>

                @error('tahun_ajaran')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Tanggal Mulai --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Tanggal Mulai
                </label>

                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $minDate) }}" min="{{ $minDate }}"
                    class="w-full border rounded px-3 py-2
                          @error('tanggal_mulai') border-red-500 @enderror" required>
            </div>


            {{-- Tanggal Selesai --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Tanggal Selesai
                </label>

                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full border rounded px-3 py-2
                                      @error('tanggal_selesai') border-red-500 @enderror" required>
            </div>


            {{-- Keterangan --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Keterangan
                </label>

                <textarea name="keterangan" rows="3"
                    class="w-full border rounded px-3 py-2">{{ old('keterangan') }}</textarea>
            </div>


            {{-- Status --}}
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">
                    Status
                </label>

                <select name="is_active" class="w-full border rounded px-3 py-2">
                    <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>
                        Aktif
                    </option>
                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>
                        Nonaktif
                    </option>
                </select>
            </div>


            {{-- BUTTON --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.periode.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>

                <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Simpan Periode
                </button>
            </div>

        </form>

        <script>
            const mulai = document.querySelector('input[name="tanggal_mulai"]');
            const selesai = document.querySelector('input[name="tanggal_selesai"]');

            function updateTanggal() {

                selesai.min = mulai.value;

                if (!selesai.value) {
                    selesai.value = mulai.value;
                }

            }

            mulai.addEventListener('change', updateTanggal);

            document.addEventListener("DOMContentLoaded", updateTanggal);
        </script>
    </div>

@endsection