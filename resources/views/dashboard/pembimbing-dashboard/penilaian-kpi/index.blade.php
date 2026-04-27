@extends('dashboard.pembimbing-dashboard.layout')

@section('content')

    @if (!$isDalamMasaPkl)

        <div class="p-6 bg-gray-50 min-h-screen">
            <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg">
                <h2 class="font-semibold">Penilaian KPI Tidak Tersedia</h2>
                <p>Penilaian KPI hanya dapat dilakukan selama masa PKL.</p>
            </div>
        </div>

    @else

        <div class="p-6 bg-gray-50 min-h-screen">

            <h1 class="text-2xl font-bold mb-4">
                Penilaian KPI Siswa
            </h1>

            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded mb-6 text-sm">
                ⚠️ Penilaian KPI hanya dapat dilakukan <strong>1 kali dalam 1 hari</strong> untuk setiap siswa.<br>
                Jika hari ini siswa <strong>tidak memiliki pekerjaan teknis</strong>, Anda dapat <strong>mengosongkan aspek
                    teknis</strong> dan hanya menilai aspek non-teknis.
            </div>

            <form method="POST" action="{{ route('pembimbing.penilaian-kpi.store') }}"
                class="bg-white p-6 rounded shadow space-y-8">
                @csrf

                {{-- ================= PILIH SISWA ================= --}}
                <div>
                    <label class="font-medium">Siswa PKL</label>

                    <select name="penempatan_pkl_id" class="w-full border rounded px-3 py-2" required>

                        <option value="">Pilih Siswa</option>

                        @foreach ($penempatan as $p)

                            @php
                                $sudahDinilai = in_array($p->id, $dinilaiHariIni ?? []);
                            @endphp

                            <option value="{{ $p->id }}" {{ $sudahDinilai ? 'disabled' : '' }}>

                                {{ $p->siswa->nama_lengkap }}
                                — {{ $p->tempat->nama_perusahaan }}
                                {{ $sudahDinilai ? '(Sudah dinilai hari ini)' : '' }}

                            </option>

                        @endforeach
                    </select>

                    <p class="text-xs text-gray-500 mt-1">
                        Siswa yang sudah dinilai hari ini akan terkunci otomatis.
                    </p>
                </div>

                {{-- ================= ASPEK TEKNIS (OPSIONAL) ================= --}}
                <div class="border rounded p-4 bg-gray-50">
                    <h2 class="font-semibold text-lg mb-2">
                        Aspek Teknis <span class="text-sm text-gray-500">(Opsional)</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-4">
                        Isi hanya jika hari ini siswa melakukan pekerjaan teknis.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium">Jenis Pekerjaan</label>
                            <input type="text" name="teknis[nama]" placeholder="Contoh: Rakit CCTV / Instalasi Jaringan"
                                class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Nilai Teknis</label>
                            <input type="number" name="teknis[nilai]" min="0" max="100" class="w-full border rounded px-3 py-2">
                        </div>
                    </div>
                </div>

                {{-- ================= ASPEK NON TEKNIS (WAJIB) ================= --}}
                <div class="border rounded p-4">
                    <h2 class="font-semibold text-lg mb-4">Aspek Non-Teknis</h2>

                    @php
                        $nonTeknis = [
                            'Disiplin',
                            'Ketertiban',
                            'Kerajinan',
                            'Kerjasama',
                            'Inisiatif & Kreatif',
                            'Tanggung Jawab',
                            'Kemampuan Kerja',
                            'Motivasi',
                            'Kebersihan',
                            'Kerapian',
                            'Kualitas Kerja',
                            'Sopan Santun'
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ($nonTeknis as $item)
                            <div>
                                <label class="text-sm font-medium">{{ $item }}</label>
                                <input type="number" name="non_teknis[{{ $item }}]" min="0" max="100"
                                    class="w-full border rounded px-3 py-2" required>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ================= ASPEK OTOMATIS ================= --}}
                <div class="border rounded p-4 bg-gray-50 text-sm text-gray-600 space-y-2">
                    <p><strong>Aspek Presensi</strong> dihitung otomatis dari sistem kehadiran.</p>
                    <p><strong>Aspek Laporan Kegiatan</strong> dihitung dari laporan yang tervalidasi.</p>
                </div>

                {{-- ================= SUBMIT ================= --}}
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Simpan Penilaian KPI
                </button>
            </form>

        </div>

    @endif

@endsection