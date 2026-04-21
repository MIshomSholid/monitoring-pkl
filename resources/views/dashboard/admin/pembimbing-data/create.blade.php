@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Tambah Pembimbing Lapangan</h1>

<div class="bg-white border rounded-lg p-6 max-w-xl">
<form method="POST"
      action="{{ route('admin.pembimbing.store') }}"
      enctype="multipart/form-data">
@csrf

<div class="mb-4">
    <label class="block text-sm font-medium">Akun Login</label>
    <select name="user_id" class="w-full border rounded px-3 py-2" required>
        <option value="">-- Pilih Akun --</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}">
                {{ $user->username }} ({{ $user->email }})
            </option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium">Nama Lengkap</label>
    <input type="text" name="nama_lengkap"
           class="w-full border rounded px-3 py-2" required>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium">Jabatan</label>
    <input type="text" name="jabatan"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-4">
    <label class="block text-sm font-medium">No Telepon</label>
    <input type="text" name="no_telepon"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-4">
    <label class="block text-sm font-medium">Email Perusahaan</label>
    <input type="email" name="email_perusahaan"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-6">
    <label class="block text-sm font-medium">Foto Profil</label>
    <input type="file" name="foto_profil"
           class="w-full border rounded px-3 py-2">
</div>

<div class="flex justify-end gap-3">
    <a href="{{ route('admin.pembimbing.index') }}"
       class="px-4 py-2 border rounded">Batal</a>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">
        Simpan
    </button>
</div>

</form>
</div>
@endsection
