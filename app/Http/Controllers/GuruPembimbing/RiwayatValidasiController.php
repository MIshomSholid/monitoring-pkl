<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use App\Models\GuruPembimbing;
use App\Models\LaporanKegiatan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatValidasiController extends Controller
{
    public function index(Request $request)
    {
        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        $query = LaporanKegiatan::with([
                'penempatanPkl.siswa'
            ])
            ->whereHas('penempatanPkl', function ($q) use ($guru) {
                $q->where('guru_pembimbing_id', $guru->id);
            });

        /* ===== FILTER SISWA ===== */
        if ($request->filled('siswa_id')) {
            $query->whereHas('penempatanPkl', function ($q) use ($request) {
                $q->where('siswa_id', $request->siswa_id);
            });
        }

        /* ===== FILTER STATUS ===== */
        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        /* ===== FILTER TANGGAL ===== */
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $laporan = $query
            ->orderBy('tanggal', 'desc')
            ->get();

        /* ===== DROPDOWN SISWA ===== */
        $daftarSiswa = Siswa::whereHas('penempatanPkl', function ($q) use ($guru) {
                $q->where('guru_pembimbing_id', $guru->id);
            })
            ->orderBy('nama_lengkap')
            ->get();

        return view(
            'dashboard.guru-dashboard.riwayat-validasi.index',
            compact('laporan', 'daftarSiswa')
        );
    }
}
