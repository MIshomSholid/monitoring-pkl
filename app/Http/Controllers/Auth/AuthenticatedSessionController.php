<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PenempatanPkl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:admin,siswa,guru_pembimbing,pembimbing_lapangan'],
        ]);

        // Autentikasi email,password & email aktif
        if (
            !Auth::attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password,
                    'is_active' => 1
                ],
                $request->boolean('remember')
            )
        ) {
            return back()->withErrors([
                'email' => 'Email/password salah atau akun dinonaktifkan.',
            ]);
        }

        $user = Auth::user();

        // Validasi role sesuai pilihan user
        if ($user->role !== $request->role) {
            Auth::logout();

            return back()->withErrors([
                'role' => 'Role yang dipilih tidak sesuai dengan akun ini.',
            ]);
        }

        // Regenerasi session
        $request->session()->regenerate();

        // ================= VALIDASI PENEMPATAN =================

        // SISWA
        if ($user->role === 'siswa') {

            if (!$user->siswa) {
                abort(403, 'Data siswa tidak ditemukan.');
            }

            $penempatan = PenempatanPkl::withoutGlobalScope('aktif')
                ->where('siswa_id', $user->siswa->id)
                ->where('status', 'aktif')
                ->first();

            if (!$penempatan) {
                abort(403, 'Penempatan PKL belum aktif atau belum divalidasi.');
            }
        }

        // PEMBIMBING LAPANGAN
        if ($user->role === 'pembimbing_lapangan') {

            $pembimbing = $user->pembimbingLapangan;

            if (!$pembimbing) {
                abort(403, 'Data pembimbing tidak ditemukan.');
            }

            $pembimbingId = $pembimbing->id;

            $hasActiveSiswa = PenempatanPkl::where('pembimbing_lapangan_id', $pembimbingId)
                ->exists();

            if (!$hasActiveSiswa) {
                abort(403, 'Semua siswa tidak aktif pada periode berjalan.');
            }
        }

        // ================= REDIRECT =================
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
            'guru_pembimbing' => redirect()->route('guru.dashboard'),
            'pembimbing_lapangan' => redirect()->route('pembimbing.dashboard'),
            default => redirect('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
