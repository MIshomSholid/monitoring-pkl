@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Tambah Informasi PKL</h1>

<div class="bg-white border rounded-lg p-6 max-w-2xl">
<form method="POST" action="{{ route('admin.informasi-pkl.store') }}">
@csrf

{{-- Judul --}}
<div class="mb-4">
    <label class="block text-sm font-medium mb-1">
        Judul Informasi
    </label>
    <input type="text"
           name="judul"
           value="{{ old('judul') }}"
           class="w-full border rounded px-3 py-2"
           required>
</div>

{{-- Konten --}}
<div class="mb-4">
    <label class="block text-sm font-medium mb-1">
        Konten Informasi
    </label>
    <textarea name="konten"
              rows="5"
              class="w-full border rounded px-3 py-2"
              required>{{ old('konten') }}</textarea>
</div>

{{-- Tipe --}}
<div class="mb-4">
    <label class="block text-sm font-medium mb-1">
        Tipe Informasi
    </label>
    <select name="tipe"
            class="w-full border rounded px-3 py-2"
            required>
        <option value="">-- Pilih Tipe --</option>
        <option value="informasi" {{ old('tipe')=='informasi'?'selected':'' }}>
            Informasi
        </option>
        <option value="jadwal" {{ old('tipe')=='jadwal'?'selected':'' }}>
            Jadwal
        </option>
        <option value="ketentuan" {{ old('tipe')=='ketentuan'?'selected':'' }}>
            Ketentuan
        </option>
    </select>
</div>

{{-- Tanggal Publish --}}
<div class="mb-4">
    <label class="block text-sm font-medium mb-1">
        Tanggal Publish
    </label>
    <input type="date"
           name="tanggal_publish"
           value="{{ old('tanggal_publish') }}"
           class="w-full border rounded px-3 py-2"
           required>
</div>

{{-- Status Publish --}}
<div class="mb-6">
    <label class="block text-sm font-medium mb-1">
        Status Publikasi
    </label>
    <select name="is_published"
            class="w-full border rounded px-3 py-2">
        <option value="1" {{ old('is_published',1)==1?'selected':'' }}>
            Dipublikasikan
        </option>
        <option value="0" {{ old('is_published')==0?'selected':'' }}>
            Draft
        </option>
    </select>
</div>

{{-- Action --}}
<div class="flex justify-end gap-3">
    <a href="{{ route('admin.informasi-pkl.index') }}"
       class="px-4 py-2 border rounded">
        Batal
    </a>

    <button class="px-4 py-2 bg-indigo-600 text-white rounded">
        Simpan Informasi
    </button>
</div>

</form>
</div>
@endsection
