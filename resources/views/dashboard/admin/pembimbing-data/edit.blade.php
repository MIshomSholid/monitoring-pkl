@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Pembimbing Lapangan</h1>

<div class="bg-white border rounded-lg p-6 max-w-xl">
<form method="POST"
      action="{{ route('admin.pembimbing.update', $pembimbing) }}"
      enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="mb-4">
    <label class="block text-sm font-medium">Nama Lengkap</label>
    <input type="text" name="nama_lengkap"
           value="{{ old('nama_lengkap', $pembimbing->nama_lengkap) }}"
           class="w-full border rounded px-3 py-2" required>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium">Jabatan</label>
    <input type="text" name="jabatan"
           value="{{ old('jabatan', $pembimbing->jabatan) }}"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-4">
    <label class="block text-sm font-medium">No Telepon</label>
    <input type="text" name="no_telepon"
           value="{{ old('no_telepon', $pembimbing->no_telepon) }}"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-4">
    <label class="block text-sm font-medium">Email Perusahaan</label>
    <input type="email" name="email_perusahaan"
           value="{{ old('email_perusahaan', $pembimbing->email_perusahaan) }}"
           class="w-full border rounded px-3 py-2">
</div>

{{-- FOTO PROFIL --}}
<div class="mb-6">
    <label class="block text-sm font-medium mb-1">Foto Profil</label>
    <input type="file"
           name="foto_profil"
           accept="image/*"
           class="w-full border rounded px-3 py-2">

    {{-- Foto Saat Ini --}}
    @if ($pembimbing->foto_profil)
        <p class="text-sm text-gray-500 mt-2">Foto saat ini:</p>
        <img src="{{ $pembimbing->foto_profil }}"
             alt="Foto Profil Pembimbing"
             class="h-24 mt-2 rounded border object-cover">
    @else
        <p class="text-sm text-gray-400 mt-2">
            Belum ada foto profil
        </p>
    @endif
</div>

<div class="flex justify-end gap-3">
    <a href="{{ route('admin.pembimbing.index') }}"
       class="px-4 py-2 border rounded">
        Batal
    </a>

    <button class="px-4 py-2 bg-indigo-600 text-white rounded">
        Simpan Perubahan
    </button>
</div>

</form>
</div>
@endsection
