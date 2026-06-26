<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//Tidak Dipakai, karena sudah menggunakan AuthenticatedSessionController.php

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:admin,siswa,guru_pembimbing,pembimbing_lapangan',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // VALIDASI ROLE
            if ($user->role !== $request->role) {
                Auth::logout();
                return back()->withErrors([
                    'role' => 'Role tidak sesuai dengan akun.'
                ]);
            }

            // REDIRECT SESUAI ROLE
            return match ($user->role) {
                'admin' => redirect('/admin/dashboard'),
                'siswa' => redirect('/siswa/dashboard'),
                'guru_pembimbing' => redirect('/guru/dashboard'),
                'pembimbing_lapangan' => redirect('/pembimbing/dashboard'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.'
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
