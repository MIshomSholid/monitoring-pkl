<x-app-layout>
    <div x-data="{ sidebarOpen: false }" class="flex h-[calc(100vh-4rem)] bg-gray-100">

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
            <div class="p-6 border-b">
                <h2 class="text-lg font-bold text-indigo-600">Admin Panel</h2>
                <p class="text-xs text-gray-500 mt-1">Sistem Monitoring PKL</p>
            </div>

            {{-- Navigation --}}
            <nav x-data="{
        openUser: false,
        init() {
            this.openUser = {{ request()->is('admin/users*', 'admin/admin-data*', 'admin/siswa*', 'admin/guru*', 'admin/pembimbing*') ? 'true' : 'false' }}
        }
    }" class="p-4 space-y-1 text-sm font-medium text-gray-700 overflow-y-auto">

                {{-- ===== MENU UTAMA ===== --}}
                <p class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">
                    Menu Utama
                </p>

                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
       {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                    Dashboard
                </a>

                {{-- ===== DROPDOWN MANAJEMEN PENGGUNA ===== --}}
                <button type="button" @click="openUser = !openUser"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-md hover:bg-indigo-50">
                    <span>Manajemen Pengguna</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openUser }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="openUser" x-transition x-cloak class="ml-4 space-y-1">

                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
           {{ request()->is('admin/users*') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                        Akun Pengguna
                    </a>

                    <a href="{{ route('admin.admin-data.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
       {{ request()->is('admin/admin-data*') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                        Data Admin
                    </a>

                    <a href="{{ route('admin.siswa.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
           {{ request()->is('admin/siswa*') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                        Data Siswa
                    </a>

                    <a href="{{ route('admin.guru.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
           {{ request()->is('admin/guru*') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                        Data Guru Pembimbing
                    </a>

                    <a href="{{ route('admin.pembimbing.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
           {{ request()->is('admin/pembimbing*') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                        Data Pembimbing Lapangan
                    </a>

                </div>

                {{-- ===== MENU UTAMA TANPA DROPDOWN ===== --}}
                <a href="{{ route('admin.tempat-pkl.index') }}"
                    class="block px-4 py-2 rounded-md hover:bg-indigo-50
                    {{ request()->routeIs('admin.tempat-pkl.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Manajemen Tempat PKL
                </a>

                <a href="{{ route('admin.penempatan.index') }}"
                    class="block px-4 py-2 rounded-md hover:bg-indigo-50
                        {{ request()->routeIs('admin.penempatan.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Penempatan PKL
                </a>

                <a href="{{ route('admin.periode.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
                    {{ request()->routeIs('admin.periode.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Periode PKL
                </a>

                <a href="{{ route('admin.informasi-pkl.index') }}"
                    class="block px-4 py-2 rounded-md hover:bg-indigo-50
                    {{ request()->routeIs('admin.informasi-pkl.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Informasi & Jadwal PKL
                </a>

                <a href="{{ route('admin.kpi-bobot') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
                    {{ request()->routeIs('admin.kpi-bobot') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Pengaturan Bobot KPI
                </a>

                {{-- ===== MONITORING & LAPORAN ===== --}}
                <p class="px-4 py-2 mt-4 text-xs text-gray-400 uppercase tracking-wider"> Monitoring & Laporan </p>
                <a href="{{ route('admin.rekap-presensi') }}"
                    class="block px-4 py-2 rounded-md hover:bg-indigo-50
                    {{ request()->routeIs('admin.rekap-presensi') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Rekap Presensi
                </a>

                <a href="{{ route('admin.rekap-laporan') }}"
                    class="block px-4 py-2 rounded-md hover:bg-indigo-50
                    {{ request()->routeIs('admin.rekap-laporan') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Rekap Laporan
                </a>

                <a href="{{ route('admin.rekap-kpi.index') }}" class="block px-4 py-2 rounded-md hover:bg-indigo-50
                    {{ request()->routeIs('admin.rekap-kpi*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Rekap KPI
                </a>

                <a href="{{ route('admin.laporan-akhir.index') }}"
                    class="block px-4 py-2 rounded-md hover:bg-indigo-50
                    {{ request()->routeIs('admin.laporan-akhir.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                    Laporan Akhir PKL
                </a>

                <hr class="my-4">

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-red-600 px-4 py-2 rounded-md hover:bg-red-50">
                        Logout
                    </button>
                </form>

            </nav>
        </aside>

        {{-- ================= CONTENT ================= --}}
        <div class="flex-1 flex flex-col">

            {{-- MOBILE TOP BAR --}}
            <header class="md:hidden bg-white border-b px-4 py-3 flex items-center gap-3">
                <button @click="sidebarOpen = true" class="text-gray-700">
                    ☰
                </button>
                <span class="font-semibold text-gray-700">Admin Panel</span>
            </header>

            <main class="flex-1 p-4 md:p-8 overflow-y-auto">
                @yield('content')
            </main>

        </div>
    </div>

    @include('components.realtime')
</x-app-layout>