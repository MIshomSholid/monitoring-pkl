@extends('dashboard.pembimbing-dashboard.layout')

@section('content')

    <h1 class="text-2xl font-bold mb-6">
        Riwayat Penilaian KPI
    </h1>

    <div class="bg-white p-5 rounded shadow mb-6 text-sm text-gray-700">
        Pilih siswa terlebih dahulu untuk melihat riwayat penilaian KPI.
    </div>

    {{-- ================= FILTER SISWA ================= --}}
    <div class="bg-white border rounded-lg p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4 text-sm">

            {{-- SISWA --}}
            <div class="w-full sm:w-80">
                <label class="block mb-1 font-medium text-gray-700">
                    Siswa PKL
                </label>

                <select name="penempatan_pkl_id" class="w-full border rounded px-3 py-2 h-10" required>

                    <option value="">Semua Siswa</option>

                    @foreach ($penempatanList as $p)
                        <option value="{{ $p->id }}" {{ request('penempatan_pkl_id') == $p->id ? 'selected' : '' }}>

                            {{ optional($p->siswa)->nama_lengkap ?? '-' }} — {{ optional($p->tempatPkl)->nama_perusahaan ?? '-' }}

                        </option>
                    @endforeach

                </select>
            </div>

            {{-- TANGGAL --}}
            <div class="w-full sm:w-56">
                <label class="block mb-1 font-medium text-gray-700">
                    Tanggal Penilaian
                </label>

                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="w-full border rounded px-3 py-2 h-10">
            </div>

            {{-- ACTION --}}
            <div class="flex gap-2">

                <button type="submit" class="h-10 px-5 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Tampilkan
                </button>

                <a href="{{ route('pembimbing.riwayat-kpi.index') }}"
                    class="h-10 px-5 bg-gray-100 rounded hover:bg-gray-200 flex items-center">
                    Reset
                </a>

            </div>

        </form>
    </div>

    {{-- ================= DATA ================= --}}
    @if ($data->count())

        @php
            $first = $data->first();
        @endphp

        <div class="space-y-6">

            <div class="bg-white p-6 rounded shadow">

                {{-- HEADER TANGGAL --}}
                <div class="border-b pb-3 mb-4">

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 text-sm">

                        <div>
                            <p class="font-semibold text-gray-800">
                                Tanggal Penilaian:
                                {{ \Carbon\Carbon::parse($tanggalAktif)->translatedFormat('d F Y') }}
                            </p>

                            <p class="text-gray-600">
                                {{ optional(optional($first->penempatanPkl)->siswa)->nama_lengkap ?? '-' }}
                                : {{ optional(optional($first->penempatanPkl)->tempatPkl)->nama_perusahaan ?? '-' }}
                            </p>
                        </div>

                        <div class="text-gray-500">
                            Total Indikator: {{ $data->count() }}
                        </div>

                    </div>

                </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden md:block">

                    <table class="w-full text-sm border">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Kategori</th>
                                <th class="px-4 py-2 text-left">Indikator</th>
                                <th class="px-4 py-2 text-center">Nilai</th>
                                <th class="px-4 py-2 text-center">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">

                            @foreach ($data as $item)

                                <tr>

                                    <td class="px-4 py-2">
                                        {{ $item->indikator->kategori->nama_kategori ?? '-' }}
                                    </td>

                                    <td class="px-4 py-2">
                                        {{ $item->indikator->nama_indikator }}
                                    </td>

                                    <td class="px-4 py-2 text-center font-semibold">
                                        {{ number_format($item->nilai, 2) }}
                                    </td>

                                    <td class="px-4 py-2 text-center">

                                        @if ($item->status_validasi === 'menunggu')
                                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">
                                                Menunggu
                                            </span>

                                        @elseif ($item->status_validasi === 'diterima')
                                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                                Disetujui
                                            </span>

                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                                Ditolak
                                            </span>
                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- NAVIGASI HARI --}}
                <div class="flex justify-between items-center mt-6 text-sm">

                    @if ($prev)
                            <a href="{{ route('pembimbing.riwayat-kpi.index', [
                            'penempatan_pkl_id' => request('penempatan_pkl_id'),
                            'tanggal' => $prev
                        ]) }}" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                ← Hari Sebelumnya
                            </a>
                    @else
                        <div></div>
                    @endif

                    @if ($next)
                            <a href="{{ route('pembimbing.riwayat-kpi.index', [
                            'penempatan_pkl_id' => request('penempatan_pkl_id'),
                            'tanggal' => $next
                        ]) }}" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                Hari Berikutnya →
                            </a>
                    @endif

                </div>

                {{-- ================= MOBILE CARD ================= --}}
                <div class="md:hidden space-y-3">

                    @foreach ($data as $item)

                        <div class="border rounded-lg p-4 text-sm space-y-2">

                            <div>
                                <p class="text-xs text-gray-500">Kategori</p>
                                <p class="font-medium">
                                    {{ $item->indikator->kategori->nama_kategori ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Indikator</p>
                                <p>
                                    {{ $item->indikator->nama_indikator }}
                                </p>
                            </div>

                            <div class="flex justify-between items-center pt-1">

                                <div>
                                    <p class="text-xs text-gray-500">Nilai</p>
                                    <p class="font-semibold">
                                        {{ number_format($item->nilai, 2) }}
                                    </p>
                                </div>

                                <div>

                                    @if ($item->status_validasi === 'menunggu')
                                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">
                                            Menunggu
                                        </span>

                                    @elseif ($item->status_validasi === 'diterima')
                                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                            Disetujui
                                        </span>

                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                            Ditolak
                                        </span>
                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    @else

        <div class="bg-white p-6 rounded shadow text-center text-gray-500">
            Silakan pilih siswa untuk melihat riwayat KPI.
        </div>

    @endif

@endsection