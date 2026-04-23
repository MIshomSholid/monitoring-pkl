<?php

namespace App\Http\Controllers\PembimbingLapangan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\PenempatanPkl;
use App\Models\Presensi;
use App\Models\PembimbingLapangan;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $pembimbing = PembimbingLapangan::where('user_id', $userId)->first();

        if (!$pembimbing) {
            return view('dashboard.pembimbing-dashboard.index', [
                'jumlahSiswa' => 0,
                'presensiHariIni' => 0,
                'presensiMenunggu' => 0,
            ]);
        }

        $penempatanIds = PenempatanPkl::where('pembimbing_lapangan_id', $pembimbing->id)
            ->where('status', 'aktif')
            ->where('status_validasi', 'diterima')
            ->pluck('id');

        if ($penempatanIds->isEmpty()) {
            abort(403, 'Belum ada siswa PKL yang disetujui.');
        }

        // ======================
        // JUMLAH SISWA PKL
        // ======================
        $jumlahSiswa = $penempatanIds->count();

        // ======================
        // PRESENSI HARI INI
        // ======================
        $presensiHariIni = Presensi::whereIn('penempatan_pkl_id', $penempatanIds)
            ->whereDate('tanggal', $today)
            ->count();

        // ======================
        // PRESENSI MENUNGGU VALIDASI
        // ======================
        $presensiMenunggu = Presensi::whereIn('penempatan_pkl_id', $penempatanIds)
            ->where('status_validasi', 'menunggu')
            ->count();

        return view('dashboard.pembimbing-dashboard.index', compact(
            'jumlahSiswa',
            'presensiHariIni',
            'presensiMenunggu'
        ));
    }
}