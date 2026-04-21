<x-app-layout>
    <div x-data="{ sidebarOpen: false }" class="flex h-[calc(100vh-64px)] bg-gray-100">

        {{-- ================= MOBILE OVERLAY ================= --}}
        <div x-show="sidebarOpen" x-transition.opacity x-cloak @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-30 md:hidden">
        </div>

        {{-- ================= SIDEBAR ================= --}}
        <aside class="
            fixed md:relative
            top-16 md:top-0
            left-0
            w-64
            h-[calc(100dvh-4rem)] md:h-full
            bg-white
            border-r border-gray-200
            z-40
            transform md:translate-x-0
            transition-transform duration-200 ease-in-out
            flex flex-col
        " :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

            {{-- Header --}}
            <div class="p-6 border-b shrink-0">
                <h2 class="text-lg font-bold text-indigo-600">
                    Dashboard Siswa
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Sistem Monitoring PKL
                </p>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto p-4 space-y-1 text-sm font-medium text-gray-700">

                {{-- ================= MENU UTAMA ================= --}}
                <p class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">
                    Menu Utama
                </p>

                <a href="{{ route('siswa.dashboard') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('siswa.dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('siswa.profil') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('siswa.profil') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Profil & Informasi PKL
                </a>


                {{-- ================= AKTIVITAS HARIAN ================= --}}
                <p class="px-4 mt-6 py-2 text-xs text-gray-400 uppercase tracking-wider">
                    Aktivitas Harian
                </p>

                <a href="{{ route('siswa.presensi') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('siswa.presensi') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Presensi
                </a>

                <a href="{{ route('siswa.laporan-kegiatan.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('siswa.laporan-kegiatan.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Laporan Kegiatan
                </a>


                {{-- ================= MONITORING & EVALUASI ================= --}}
                <p class="px-4 mt-6 py-2 text-xs text-gray-400 uppercase tracking-wider">
                    Monitoring & Evaluasi
                </p>

                <a href="{{ route('siswa.kpi.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('siswa.kpi.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Monitoring KPI
                </a>

                <a href="{{ route('siswa.evaluasi') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('siswa.evaluasi') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Catatan & Evaluasi
                </a>


                {{-- ================= RIWAYAT & ARSIP ================= --}}
                <p class="px-4 mt-6 py-2 text-xs text-gray-400 uppercase tracking-wider">
                    Riwayat & Arsip
                </p>

                <a href="{{ route('siswa.riwayat-presensi') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('siswa.riwayat-presensi') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Riwayat Presensi
                </a>

                <a href="{{ route('siswa.riwayat-laporan') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('siswa.riwayat-laporan') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Riwayat Laporan
                </a>

                <hr class="my-4">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-red-600 px-4 py-2 rounded-md hover:bg-red-50">
                        Logout
                    </button>
                </form>

            </nav>
        </aside>

        {{-- ================= CONTENT ================= --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- MOBILE TOP BAR --}}
            <header class="md:hidden bg-white border-b px-4 py-3 flex items-center gap-3 shrink-0">
                <button @click="sidebarOpen = true" class="text-gray-700 text-xl">
                    ☰
                </button>
                <span class="font-semibold text-gray-700">
                    Dashboard Siswa
                </span>
            </header>

            {{-- CONTENT AREA --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                @yield('content')
            </main>

        </div>
    </div>

    @include('components.realtime')
</x-app-layout>