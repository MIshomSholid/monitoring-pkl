<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Monitoring PKL') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-100">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

        {{-- Logo SMKN 7 --}}
        <div class="mb-6 text-center">
            <a href="/">
                <img src="{{ asset('images/logo-smkn7.jpeg') }}"
                     alt="Logo SMKN 7"
                     class="w-20 mx-auto mb-3 object-contain">
            </a>

            <h2 class="text-lg font-bold text-slate-800">
                SMKN 7 Surabaya
            </h2>

            <p class="text-sm text-slate-500">
                Sistem Monitoring PKL
            </p>
        </div>

        {{-- Card Login --}}
        <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-lg rounded-lg">
            {{ $slot }}
        </div>

    </div>

</body>
</html>
