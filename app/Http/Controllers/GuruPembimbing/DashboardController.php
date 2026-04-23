<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\GuruPembimbing;
use App\Models\PenempatanPkl;
use App\Models\Presensi;
use App\Models\LaporanKegiatan;
use App\Models\PenilaianKpi;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $guru = GuruPembimbing::where('user_id', $userId)->first();

        if (!$guru) {
            return view('dashboard.guru-dashboard.index', [
                'jumlahSiswa' => 0,
                'presensiHariIni' => 0,
                'laporanMenunggu' => 0,
                'kpiMenunggu' => 0,
            ]);
        }

        // Ambil semua penempatan siswa yang dibimbing
        $penempatanIds = PenempatanPkl::where('guru_pembimbing_id', $guru->id)
            ->where('status', 'aktif')
            ->where('status_validasi', 'diterima')
            ->pluck('id');

        // =====================
        // JUMLAH SISWA BIMBINGAN
        // =====================
        $jumlahSiswa = $penempatanIds->count();

        // =====================
        // PRESENSI HARI INI (SUDAH DIVALIDASI LAPANGAN)
        // =====================
        $presensiHariIni = Presensi::whereIn('penempatan_pkl_id', $penempatanIds)
            ->whereDate('tanggal', $today)
            ->where('status_validasi', 'diterima')
            ->count();

        // =====================
        // LAPORAN MENUNGGU VALIDASI GURU
        // =====================
        $laporanMenunggu = LaporanKegiatan::whereIn('penempatan_pkl_id', $penempatanIds)
            ->where('status_validasi', 'menunggu')
            ->count();

        // =====================
        // KPI MENUNGGU VALIDASI GURU
        // =====================
        $kpiMenunggu = PenilaianKpi::whereIn('penempatan_pkl_id', $penempatanIds)
            ->where('status_validasi', 'menunggu')
            ->count();

        return view('dashboard.guru-dashboard.index', compact(
            'jumlahSiswa',
            'presensiHariIni',
            'laporanMenunggu',
            'kpiMenunggu'
        ));
    }
}