@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Tambah Tempat PKL</h1>

<div class="bg-white border rounded p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.tempat-pkl.store') }}">
        @csrf

        @include('dashboard.admin.tempat-pkl.form')

        <div class="mt-6 flex items-center gap-3">
            <a href="{{ route('admin.tempat-pkl.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                Batal
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition">
                Simpan
            </button>
        </div>

    </form>
</div>
@endsection