@extends('dashboard.admin.layout')

@section('content')

    @php
        use Illuminate\Support\Str;
    @endphp

    <div class="p-6 bg-gray-50 min-h-screen">

        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            Pengaturan Bobot KPI
        </h1>


        {{-- TOTAL BOBOT --}}
        <div class="mb-6">
            <div id="totalBox" class="inline-block px-4 py-2 rounded-lg bg-gray-200 text-gray-800 font-semibold">

                Total Bobot : <span id="totalBobot">0.00</span> %

            </div>
        </div>



        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif



        <form method="POST" action="{{ route('admin.kpi-bobot.update') }}">
            @csrf


            <div class="space-y-6">

                @foreach($kategori as $k)

                                <div class="bg-white shadow rounded-xl p-6 border border-gray-100">


                                    <div class="grid md:grid-cols-2 gap-4 items-center mb-4">

                                        <h2 class="text-lg font-semibold text-gray-800">
                                            {{ $k->nama_kategori }}
                                        </h2>


                                        <div class="flex items-center gap-3 justify-start md:justify-end">

                                            <label class="text-sm text-gray-500 whitespace-nowrap">
                                                Bobot (%)
                                            </label>


                                            <input type="number" step="0.01" min="0" max="100" name="kategori[{{ $k->id }}]"
                                                value="{{ number_format($k->bobot_persen, 2, '.', '') }}" class="bobot-input w-28 border rounded-md px-3 py-1 text-center
                    focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:ring-offset-1">

                                        </div>

                                    </div>



                                    {{-- ASPEK TEKNIS --}}
                                    @if(Str::contains(strtolower($k->nama_kategori), 'teknis'))

                                        <div class="bg-indigo-50 border border-indigo-100 text-sm text-gray-700 rounded-lg p-4">

                                            Penilaian <b>Aspek Teknis</b> akan diinput langsung oleh
                                            <b>Pembimbing Lapangan</b> sesuai pekerjaan atau tugas
                                            yang dilakukan siswa selama PKL.

                                        </div>

                                    @endif




                                    {{-- NON TEKNIS --}}
                                    @if(Str::contains(strtolower($k->nama_kategori), 'non'))

                                        <div class="bg-gray-50 border rounded-lg p-4">

                                            <p class="font-semibold text-gray-700 mb-3">
                                                Indikator Penilaian (13 indikator):
                                            </p>

                                            <div class="grid md:grid-cols-2 gap-2 text-sm text-gray-700">

                                                @foreach($k->indikator as $i)

                                                    <div class="flex items-center gap-2">

                                                        <span class="text-indigo-500">•</span>
                                                        <span>{{ $i->nama_indikator }}</span>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif




                                    {{-- PRESENSI --}}
                                    @if(Str::contains(strtolower($k->nama_kategori), 'presensi'))

                                        <div class="bg-gray-50 border rounded-lg p-4 text-sm text-gray-700">

                                            Nilai presensi dihitung otomatis dari
                                            <b>sistem presensi PKL</b> berdasarkan
                                            kehadiran siswa selama PKL.

                                        </div>

                                    @endif




                                    {{-- LAPORAN --}}
                                    @if(Str::contains(strtolower($k->nama_kategori), 'laporan'))

                                        <div class="bg-gray-50 border rounded-lg p-4 text-sm text-gray-700">

                                            Nilai laporan diambil dari
                                            <b>laporan kegiatan harian</b>
                                            yang diisi siswa dan divalidasi oleh Guru Pembimbing.

                                        </div>

                                    @endif


                                </div>

                @endforeach

            </div>



            <button class="mt-8 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow">
                Simpan Bobot
            </button>


        </form>

    </div>




    {{-- ================= SCRIPT LIVE TOTAL ================= --}}
    <script>

        function hitungTotal() {

            let inputs = document.querySelectorAll('.bobot-input')

            let total = 0

            inputs.forEach(input => {

                total += parseFloat(input.value) || 0

            })

            document.getElementById('totalBobot').innerText = total.toFixed(2)

            let box = document.getElementById('totalBox')

            if (total.toFixed(2) == 100.00) {

                box.className = "inline-block px-4 py-2 rounded-lg bg-green-100 text-green-700 font-semibold"

            } else {

                box.className = "inline-block px-4 py-2 rounded-lg bg-red-100 text-red-700 font-semibold"

            }

        }


        document.querySelectorAll('.bobot-input').forEach(el => {

            el.addEventListener('input', hitungTotal)

        })


        window.addEventListener('load', hitungTotal)

    </script>

@endsection