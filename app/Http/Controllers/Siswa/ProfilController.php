<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\InformasiPkl;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;

class ProfilController extends Controller
{
    public function index()
{
    $user = Auth::user();

    $siswa = Siswa::with([
        'penempatanAktif.tempatPkl',
        'penempatanAktif.periodePkl',
        'penempatanAktif.guruPembimbing',
        'penempatanAktif.pembimbingLapangan'
    ])
    ->where('user_id', $user->id)
    ->firstOrFail();

    $penempatan = $siswa->penempatanAktif;

    // Informasi & Jadwal PKL dari Admin
    $informasiPkl = InformasiPkl::where('is_published', true)
        ->whereDate('tanggal_publish', '<=', now())
        ->orderBy('tanggal_publish', 'desc')
        ->limit(5)
        ->get();

    return view('dashboard.siswa-dashboard.profil', compact(
        'user',
        'siswa',
        'penempatan',
        'informasiPkl'
    ));
}

}
