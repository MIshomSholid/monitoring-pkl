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
                    Dashboard Pembimbing Lapangan
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

                <a href="{{ route('pembimbing.dashboard') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('pembimbing.dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('pembimbing.siswa-pkl.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('pembimbing.siswa-pkl.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Siswa PKL
                </a>


                {{-- ================= VALIDASI ================= --}}
                <p class="px-4 mt-6 py-2 text-xs text-gray-400 uppercase tracking-wider">
                    Validasi
                </p>

                <a href="{{ route('pembimbing.validasi-presensi.index') }}"
                    class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('pembimbing.validasi-presensi.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Validasi Presensi
                </a>

                <a href="{{ route('pembimbing.evaluasi.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('pembimbing.evaluasi.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Catatan & Evaluasi
                </a>


                {{-- ================= PENILAIAN  ================= --}}
                <p class="px-4 mt-6 py-2 text-xs text-gray-400 uppercase tracking-wider">
                    Penilaian
                </p>

                <a href="{{ route('pembimbing.penilaian-kpi.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('pembimbing.penilaian-kpi.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Penilaian KPI
                </a>


                {{-- ================= RIWAYAT & ARSIP ================= --}}
                <p class="px-4 mt-6 py-2 text-xs text-gray-400 uppercase tracking-wider">
                    Riwayat & Arsip
                </p>

                <a href="{{ route('pembimbing.riwayat-presensi.index') }}"
                    class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('pembimbing.riwayat-presensi.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Riwayat Presensi
                </a>

                <a href="{{ route('pembimbing.riwayat-kpi.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
        {{ request()->routeIs('pembimbing.riwayat-kpi.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Riwayat KPI
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
                    Dashboard Pembimbing Lapangan
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