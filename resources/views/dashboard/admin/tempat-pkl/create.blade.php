@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Tambah Tempat PKL</h1>

<div class="bg-white border rounded p-6 max-w-2xl">
<form method="POST" action="{{ route('admin.tempat-pkl.store') }}">
@csrf

@include('dashboard.admin.tempat-pkl.form')

<a href="{{ route('admin.tempat-pkl.index') }}"
       class="px-4 py-2 border rounded">
        Batal
    </a>

<button class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded">
    Simpan
</button>

</form>
</div>
@endsection
