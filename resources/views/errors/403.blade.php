<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>403 | Tidak Memiliki Akses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md text-center">
        <h1 class="text-2xl font-black text-red-600 mb-2">
            403
        </h1>
        <p class="text-gray-700 mb-6">
            Anda tidak memiliki akses ke halaman ini.
        </p>

        {{-- 🔥 TOMBOL LOGOUT --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition">
                Logout
            </button>
        </form>
    </div>

</body>
</html>