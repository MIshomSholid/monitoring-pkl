<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanKegiatan;

class RiwayatLaporanController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $penempatan = $siswa?->penempatanAktif;

        if (!$penempatan) {
            abort(403, 'Anda belum memiliki penempatan PKL aktif');
        }

        $laporan = LaporanKegiatan::where('penempatan_pkl_id', $penempatan->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view(
            'dashboard.siswa-dashboard.riwayat-laporan.index',
            compact('laporan')
        );
    }
}
