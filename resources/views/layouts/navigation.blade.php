<nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-100 shadow">

    @php
        $user = Auth::user();

        // ================= ROUTE DASHBOARD =================
        if ($user->role === 'admin') {
            $homeRoute = route('admin.dashboard');
        } elseif ($user->role === 'guru_pembimbing') {
            $homeRoute = route('guru.dashboard');
        } elseif ($user->role === 'pembimbing_lapangan') {
            $homeRoute = route('pembimbing.dashboard');
        } else {
            $homeRoute = route('siswa.dashboard');
        }
        // ================= FOTO USER =================
        $photo = null;

        if ($user->role === 'siswa' && $user->siswa && $user->siswa->foto_profil) {
            $photo = $user->siswa->foto_profil;
        } elseif ($user->role === 'guru_pembimbing' && $user->guruPembimbing && $user->guruPembimbing->foto_profil) {
            $photo = $user->guruPembimbing->foto_profil;
        } elseif ($user->role === 'pembimbing_lapangan' && $user->pembimbingLapangan && $user->pembimbingLapangan->foto_profil) {
            $photo = $user->pembimbingLapangan->foto_profil;
        }
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- ================= LEFT SIDE ================= --}}
            <div class="flex items-center gap-6">

                {{-- LOGO --}}
                <a href="{{ $homeRoute }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-smkn7.jpeg') }}" alt="Logo SMKN 7"
                        class="h-10 w-auto object-contain">

                    <span class="text-sm font-semibold text-gray-800 hidden md:block">
                        SMKN 7 Surabaya
                    </span>
                </a>

                {{-- NAV LINK --}}
                <x-nav-link :href="$homeRoute" :active="request()->routeIs('*dashboard')">
                    {{ __('Dashboard') }}
                </x-nav-link>

            </div>

            {{-- ================= RIGHT SIDE (USERNAME DROPDOWN) ================= --}}
            <div class="flex items-center">

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 focus:outline-none transition duration-150 ease-in-out">

                            <div class="flex items-center gap-2">

                                {{-- FOTO USER --}}
                                @if($photo)
                                    <img src="{{ $photo }}" class="w-8 h-8 rounded-full object-cover border">
                                @else
                                    {{-- avatar default --}}
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-semibold text-white">
                                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                    </div>
                                @endif

                                {{-- USERNAME --}}
                                <span>{{ $user->username }}</span>

                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                        </button>
                    </x-slot>

                    <x-slot name="content">

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>

                    </x-slot>
                </x-dropdown>

            </div>

        </div>
    </div>

</nav>