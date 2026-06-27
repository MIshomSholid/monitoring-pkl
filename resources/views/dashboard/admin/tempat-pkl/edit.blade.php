@extends('dashboard.admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Tempat PKL</h1>

<div class="bg-white border rounded p-6 max-w-2xl">
    <form method="POST"
          action="{{ route('admin.tempat-pkl.update', $tempatPkl) }}">
        @csrf
        @method('PUT')

        @include('dashboard.admin.tempat-pkl.form', ['tempatPkl' => $tempatPkl])

        <div class="mt-6 flex items-center gap-3">
            <a href="{{ route('admin.tempat-pkl.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                Batal
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition">
                Update
            </button>
        </div>

    </form>
</div>
@endsection