<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PenempatanPkl;
use App\Models\CatatanEvaluasi;

class EvaluasiSiswaController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $penempatan = PenempatanPkl::whereHas('siswa', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'aktif')
            ->first();

        if (!$penempatan) {
            return view('dashboard.siswa-dashboard.evaluasi.index', [
                'evaluasi' => collect()
            ]);
        }

        $evaluasi = CatatanEvaluasi::with('pembuat')
            ->where('penempatan_pkl_id', $penempatan->id)
            ->where('kategori', 'pembimbing_lapangan')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view(
            'dashboard.siswa-dashboard.evaluasi.index',
            compact('evaluasi')
        );
    }
}
