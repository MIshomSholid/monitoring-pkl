<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Monitoring PKL') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen antialiased">

    <div class="max-w-7xl mx-auto px-6 py-10">

        {{-- Navbar --}}
        <div class="flex justify-between items-center py-4 border-b border-slate-200">
            <div class="flex items-center gap-4">

                <img src="{{ asset('images/logo-smkn7.jpeg') }}" alt="Logo SMKN 7" class="w-14 h-14 object-contain">

                <div class="leading-tight">
                    <h1 class="text-base font-bold text-slate-900 uppercase tracking-wide">
                        SMK Negeri 7 Surabaya
                    </h1>
                    <p class="text-xs text-slate-600">
                        Sistem Informasi Monitoring PKL
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-5 py-2 bg-slate-800 text-white text-sm font-semibold rounded-md hover:bg-slate-900 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">
                        Masuk
                    </a>

                    <!-- @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="px-5 py-2 bg-slate-800 text-white text-sm font-semibold rounded-md hover:bg-slate-900 transition">
                            Daftar
                        </a>
                    @endif -->
                @endauth
            </div>
        </div>

        {{-- Hero Section --}}
        <div class="grid md:grid-cols-2 gap-16 items-center py-20">

            {{-- Left --}}
            <div>
                <h2 class="text-4xl font-bold text-slate-900 leading-tight mb-6">
                    Monitoring PKL
                    <span class="block text-slate-600 text-2xl font-medium mt-2">
                        SMKN 7 Surabaya
                    </span>
                </h2>

                <p class="text-slate-600 leading-relaxed text-lg mb-10 max-w-xl">
                    Platform terintegrasi untuk mengelola presensi, laporan kegiatan,
                    serta proses evaluasi siswa PKL secara sistematis, transparan,
                    dan akurat.
                </p>

                {{-- Features --}}
                <div class="space-y-4 text-slate-700 text-sm mb-10">

                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 mt-2 bg-slate-800 rounded-full"></div>
                        <p>Presensi berbasis lokasi (GPS)</p>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 mt-2 bg-slate-800 rounded-full"></div>
                        <p>Pencatatan laporan kegiatan harian</p>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 mt-2 bg-slate-800 rounded-full"></div>
                        <p>Validasi oleh Guru dan Pembimbing Lapangan</p>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 mt-2 bg-slate-800 rounded-full"></div>
                        <p>Rekapitulasi dan analisis kinerja siswa</p>
                    </div>

                </div>
            </div>

            {{-- Right Panel --}}
            <div>
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-10">

                    <h3 class="text-lg font-semibold text-slate-800 mb-4">
                        Informasi Sistem
                    </h3>

                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                        Sistem ini dirancang untuk mendukung proses administrasi
                        Praktik Kerja Lapangan secara terstruktur dan terdokumentasi
                        dengan baik.
                    </p>

                    <div class="border-t pt-6 space-y-4 text-sm text-slate-600">

                        <div class="flex justify-between">
                            <span>Status Sistem</span>
                            <span class="text-green-600 font-medium">Aktif</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Akses</span>
                            <span>Multi Role</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Update Terakhir</span>
                            <span>{{ date('d M Y') }}</span>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div
            class="border-t border-slate-200 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500">
            <p>
                © {{ date('Y') }} {{ config('app.name') }} | SMKN 7 Surabaya
            </p>

            <div class="flex gap-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-slate-800 transition">Panduan</a>
                <a href="#" class="hover:text-slate-800 transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-slate-800 transition">Kontak</a>
            </div>
        </div>

    </div>

    {{-- ALERT SESSION EXPIRED --}}
    @if(session('session_expired'))
        <div id="session-toast" class="fixed top-6 right-6 z-50 transform translate-x-full
                   bg-white border border-red-200 shadow-xl rounded-xl
                   p-4 w-80 flex items-start gap-3">

            <div class="flex-shrink-0 w-2 h-10 bg-red-500 rounded-full"></div>

            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800">
                    Session Berakhir
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    {{ session('session_expired') }}
                </p>
            </div>

            <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">
                ×
            </button>
        </div>

        <script>
            const toast = document.getElementById('session-toast');

            // Slide In
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
                toast.style.transition = "all 0.4s ease";
            }, 100);

            // Auto Close After 5s
            setTimeout(closeToast, 5000);

            function closeToast() {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
                toast.style.transition = "all 0.4s ease";

                setTimeout(() => {
                    toast.remove();
                }, 400);
            }
        </script>
    @endif

</body>

</html>