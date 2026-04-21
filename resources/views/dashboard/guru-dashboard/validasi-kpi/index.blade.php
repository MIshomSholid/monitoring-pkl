@extends('dashboard.guru-dashboard.layout')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex flex-col gap-4">

            {{-- ================= HEADER ================= --}}
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Validasi KPI Siswa
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Validasi penilaian KPI dari Pembimbing Lapangan
                </p>
            </div>

            {{-- ================= FILTER ================= --}}
            <div class="bg-white border rounded-lg p-4">
                <form method="GET" class="flex flex-wrap items-end gap-4 text-sm">

                    <div class="w-full sm:w-72">
                        <label class="block mb-1 font-medium text-gray-700">
                            Nama Siswa
                        </label>
                        <select name="siswa_id" class="w-full border rounded px-3 py-2 h-10">
                            <option value="">Semua Siswa</option>
                            @foreach ($daftarSiswa as $siswa)
                                <option value="{{ $siswa->id }}" {{ request('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                    {{ $siswa->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-56">
                        <label class="block mb-1 font-medium text-gray-700">
                            Tanggal Penilaian
                        </label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="w-full border rounded px-3 py-2 h-10">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="h-10 px-5 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Filter
                        </button>
                        <a href="{{ route('guru.validasi-kpi.index') }}"
                            class="h-10 px-5 bg-gray-100 rounded hover:bg-gray-200 flex items-center">
                            Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- ================= LIST KPI ================= --}}
            @forelse ($data as $penempatanId => $items)

                <div id="detail-kpi" class="bg-white border rounded-lg p-6 shadow-sm">

                    @php
                        $first = $items->first();
                    @endphp

                    {{-- HEADER TANGGAL --}}
                    <div class="border-b pb-3 mb-4">

                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 text-sm">

                            <div>
                                <p class="font-semibold text-gray-800">
                                    Tanggal Penilaian:
                                    {{ \Carbon\Carbon::parse($tanggalAktif)->translatedFormat('d F Y') }}
                                </p>

                                <p class="text-gray-600">
                                    {{ optional(optional($first->penempatanPkl)->siswa)->nama_lengkap ?? '-' }}
                                    :
                                    {{ optional($first->penempatanPkl->tempatPkl)->nama_perusahaan ?? '-' }}
                                </p>

                                <p class="text-gray-500">
                                    Total Indikator: {{ $items->count() }}
                                </p>
                            </div>

                            {{-- VALIDASI --}}
                            @php
                                $masihMenunggu = $items->contains(
                                    fn($row) => $row->status_validasi === 'menunggu'
                                );
                            @endphp

                            <div>
                                @if ($masihMenunggu)

                                    <form method="POST"
                                        action="{{ route('guru.validasi-kpi.validate', [$penempatanId, $tanggalAktif]) }}"
                                        class="flex gap-2">

                                        @csrf

                                        <button name="status" value="diterima"
                                            class="px-4 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                            Setujui
                                        </button>

                                        <button name="status" value="ditolak"
                                            class="px-4 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                            Tolak
                                        </button>

                                    </form>

                                @else

                                    <span class="text-sm text-gray-400 italic">
                                        Sudah divalidasi
                                    </span>

                                @endif
                            </div>

                        </div>

                    </div>


                    {{-- ================= TABEL KPI ================= --}}
                    <div class="hidden md:block border rounded-lg overflow-x-auto">
                        <table class="w-full text-sm">

                            <thead class="bg-gray-50 text-gray-700 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">Kategori</th>
                                    <th class="px-4 py-3 text-left">Indikator</th>
                                    <th class="px-4 py-3 text-center">Nilai</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @foreach ($items as $row)

                                    @php
                                        $statusClass = match ($row->status_validasi) {
                                            'diterima' => 'bg-green-100 text-green-700',
                                            'ditolak' => 'bg-red-100 text-red-700',
                                            default => 'bg-yellow-100 text-yellow-700'
                                        };
                                    @endphp

                                    <tr class="hover:bg-gray-50">

                                        <td class="px-4 py-2">
                                            {{ $row->indikator->kategori->nama_kategori }}
                                        </td>

                                        <td class="px-4 py-2">
                                            {{ $row->indikator->nama_indikator }}
                                        </td>

                                        <td class="px-4 py-2 text-center font-semibold">
                                            {{ $row->nilai }}
                                        </td>

                                        <td class="px-4 py-2 text-center">
                                            <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                                {{ ucfirst($row->status_validasi) }}
                                            </span>
                                        </td>

                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>


                    {{-- ================= MOBILE VERSION ================= --}}
                    <div class="md:hidden divide-y">
                        @foreach ($items as $row)

                            @php
                                $statusClass = match ($row->status_validasi) {
                                    'diterima' => 'bg-green-100 text-green-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                    default => 'bg-yellow-100 text-yellow-700'
                                };
                            @endphp

                            <div class="py-3 text-sm space-y-1">

                                <div class="font-medium">
                                    {{ $row->indikator->nama_indikator }}
                                </div>

                                <div>
                                    Kategori:
                                    {{ $row->indikator->kategori->nama_kategori }}
                                </div>

                                <div>
                                    Nilai:
                                    <span class="font-semibold">{{ $row->nilai }}</span>
                                </div>

                                <div>
                                    <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                        {{ ucfirst($row->status_validasi) }}
                                    </span>
                                </div>

                            </div>

                        @endforeach
                    </div>


                    {{-- ================= PAGINATION HARI (SAMA SEPERTI SISWA) ================= --}}
                    <div class="flex justify-between items-center mt-6 text-sm">

                        @if ($prev)
                            <a href="{{ route('guru.validasi-kpi.index', ['tanggal' => $prev]) }}#detail-kpi"
                                class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                ← Hari Sebelumnya
                            </a>
                        @else
                            <div></div>
                        @endif

                        @if ($next)
                            <a href="{{ route('guru.validasi-kpi.index', ['tanggal' => $next]) }}#detail-kpi"
                                class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                Hari Berikutnya →
                            </a>
                        @endif

                    </div>

                </div>

            @empty

                <div class="bg-white border rounded-lg p-6 text-center text-gray-500 shadow-sm">
                    Belum ada penilaian KPI untuk divalidasi
                </div>

            @endforelse

        </div>
    </div>
@endsection