<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PenempatanPkl;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            $user = Auth::user();

            // ================= USER NONAKTIF =================
            if (!$user->is_active) {

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan oleh admin.'
                ]);
            }

            // ================= PEMBIMBING LAPANGAN =================
            if ($user->role === 'pembimbing_lapangan') {

                $pembimbing = $user->pembimbingLapangan;

                if (!$pembimbing) {
                    abort(403);
                }

                $pembimbingId = $pembimbing->id;

                $hasActiveSiswa = PenempatanPkl::where('pembimbing_lapangan_id', $pembimbingId)
                    ->exists();

                if (!$hasActiveSiswa) {
                    abort(403, 'Akses ditolak: tidak ada siswa aktif.');
                }
            }
        }

        return $next($request);
    }
}