<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\TempatPkl;
use App\Models\PeriodePkl;
use App\Models\PenempatanPkl;
use App\Models\Presensi;
use App\Models\LaporanKegiatan;
use App\Models\LaporanAkhirPkl;
use App\Models\PenilaianKpi;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // =========================
        // STATISTIK UMUM
        // =========================

        $totalSiswa = PenempatanPkl::distinct('siswa_id')->count('siswa_id');
        $totalTempat = TempatPkl::count();
        $periodeAktif = PeriodePkl::aktif();
        $penempatanAktif = PenempatanPkl::where('status', 'aktif')->count();

        // =========================
        // MONITORING HARI INI
        // =========================

        $presensiHariIni = Presensi::whereDate('tanggal', $today)->count();

        $hadirHariIni = Presensi::whereDate('tanggal', today())
            ->where('jenis_presensi', 'hadir')
            ->count();

        $izinHariIni = Presensi::whereDate('tanggal', today())
            ->where('jenis_presensi', 'izin')
            ->count();

        $alphaHariIni = Presensi::whereDate('tanggal', today())
            ->where('jenis_presensi', 'alpha')
            ->count();

        $liburHariIni = Presensi::whereDate('tanggal', today())
            ->where('jenis_presensi', 'libur')
            ->count();

        $laporanHariIni = LaporanKegiatan::whereDate('tanggal', $today)->count();

        // =========================
        // STATUS VALIDASI
        // =========================

        $presensiPending = Presensi::where('status_validasi', 'menunggu')->count();
        $laporanPending = LaporanKegiatan::where('status_validasi', 'menunggu')->count();
        $laporanAkhirPending = LaporanAkhirPkl::where('status_validasi', 'menunggu')->count();
        $kpiPending = PenilaianKpi::where('status_validasi', 'menunggu')->count();

        // =========================
        // GRAFIK BULANAN (6 BULAN TERAKHIR)
        // =========================

        $bulanLabels = [];
        $hadirData = [];
        $izinData = [];
        $alphaData = [];
        $liburData = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $bulanLabels[] = $bulan->format('M Y');

            $hadirData[] = Presensi::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->where('jenis_presensi', 'hadir')
                ->count();

            $izinData[] = Presensi::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->where('jenis_presensi', 'izin')
                ->count();

            $alphaData[] = Presensi::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->where('jenis_presensi', 'alpha')
                ->count();

            $liburData[] = Presensi::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->where('jenis_presensi', 'libur')
                ->count();
        }

        // =========================
        // GRAFIK VALIDASI
        // =========================

        $validasiMenunggu =
            Presensi::where('status_validasi', 'menunggu')->count() +
            LaporanKegiatan::where('status_validasi', 'menunggu')->count() +
            PenilaianKpi::where('status_validasi', 'menunggu')->count();

        $validasiDiterima =
            Presensi::where('status_validasi', 'diterima')->count() +
            LaporanKegiatan::where('status_validasi', 'diterima')->count() +
            PenilaianKpi::where('status_validasi', 'diterima')->count();

        $validasiDitolak =
            Presensi::where('status_validasi', 'ditolak')->count() +
            LaporanKegiatan::where('status_validasi', 'ditolak')->count() +
            PenilaianKpi::where('status_validasi', 'ditolak')->count();

        $validasiData = [
            $validasiMenunggu,
            $validasiDiterima,
            $validasiDitolak
        ];

        // KOMPOSISI PRESENSI
        $totalHadir = Presensi::where('jenis_presensi', 'hadir')->count();
        $totalIzin = Presensi::where('jenis_presensi', 'izin')->count();
        $totalAlpha = Presensi::where('jenis_presensi', 'alpha')->count();
        $totalLibur = Presensi::where('jenis_presensi', 'libur')->count();

        return view('dashboard.admin.index', compact(
            'totalSiswa',
            'totalTempat',
            'periodeAktif',
            'penempatanAktif',
            'presensiHariIni',
            'laporanHariIni',
            'hadirHariIni',
            'izinHariIni',
            'alphaHariIni',
            'liburHariIni',
            'presensiPending',
            'laporanPending',
            'laporanAkhirPending',
            'kpiPending',
            'bulanLabels',
            'hadirData',
            'izinData',
            'alphaData',
            'liburData',
            'validasiData',
            'totalHadir',
            'totalIzin',
            'totalAlpha',
            'totalLibur'
        ));
    }
}