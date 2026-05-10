<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

/* ================= ADMIN CONTROLLERS ================= */
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminDataController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\GuruPembimbingController;
use App\Http\Controllers\Admin\PembimbingLapanganController;
use App\Http\Controllers\Admin\InformasiPKLController;
use App\Http\Controllers\Admin\TempatPKLController;
use App\Http\Controllers\Admin\PeriodePklController;
use App\Http\Controllers\Admin\PenempatanPklController;
use App\Http\Controllers\Admin\RekapPresensiController;
use App\Http\Controllers\Admin\RekapLaporanController;
use App\Http\Controllers\Admin\RekapKPIController;
use App\Http\Controllers\Admin\LaporanAkhirController;
use App\Http\Controllers\Admin\KpiBobotController;
/* ================= SISWA CONTROLLERS ================= */
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfilController;
use App\Http\Controllers\Siswa\PresensiController;
use App\Http\Controllers\Siswa\RiwayatPresensiController;
use App\Http\Controllers\Siswa\LaporanKegiatanController;
use App\Http\Controllers\Siswa\RiwayatLaporanController;
use App\Http\Controllers\Siswa\KPIController;
use App\Http\Controllers\Siswa\EvaluasiSiswaController;
/* ================= GURU PEMBIMBING CONTROLLERS ================= */
use App\Http\Controllers\GuruPembimbing\DashboardController as GuruDashboardController;
use App\Http\Controllers\GuruPembimbing\SiswaBimbinganController;
use App\Http\Controllers\GuruPembimbing\MonitoringPresensiController;
use App\Http\Controllers\GuruPembimbing\ValidasiLaporanController;
use App\Http\Controllers\GuruPembimbing\RiwayatValidasiController;
use App\Http\Controllers\GuruPembimbing\ValidasiKPIController;
use App\Http\Controllers\GuruPembimbing\MonitoringKPIController;
use App\Http\Controllers\GuruPembimbing\RekapSiswaController;
use App\Http\Controllers\GuruPembimbing\ValidasiTempatController;
use App\Http\Controllers\GuruPembimbing\ValidasiLaporanAkhirController;
/* ================= PEMBIMBING LAPANGAN CONTROLLERS ================= */
use App\Http\Controllers\PembimbingLapangan\DashboardController as PembimbingDashboardController;
use App\Http\Controllers\PembimbingLapangan\SiswaPKLController;
use App\Http\Controllers\PembimbingLapangan\ValidasiPresensiController;
use App\Http\Controllers\PembimbingLapangan\RiwayatPresensiPembimbingController;
use App\Http\Controllers\PembimbingLapangan\PenilaianKPIController;
use App\Http\Controllers\PembimbingLapangan\RiwayatKPIController;
use App\Http\Controllers\PembimbingLapangan\EvaluasiController;


/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {

    if (!Auth::check()) {
        return view('welcome');
    }

    $role = Auth::user()->role;

    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'siswa' => redirect()->route('siswa.dashboard'),
        'guru_pembimbing' => redirect()->route('guru.dashboard'),
        'pembimbing_lapangan' => redirect()->route('pembimbing.dashboard'),
        default => abort(403),
    };

});

/*
|--------------------------------------------------------------------------
| Dashboard Redirect (Role Based)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {

    $role = auth()->user()->role;

    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'siswa' => redirect()->route('siswa.dashboard'),
        'guru_pembimbing' => redirect()->route('guru.dashboard'),
        'pembimbing_lapangan' => redirect()->route('pembimbing.dashboard'),
        default => abort(403),
    };

})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.active'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ================= MANAJEMEN PENGGUNA =================
    
        // Akun Login
        Route::resource('/users', UserController::class);

        // ================= DATA ADMIN =================
    
        //Data Admin
        Route::get('/admin-data', [AdminDataController::class, 'index'])
            ->name('admin-data.index');

        // CREATE
        Route::get('/admin-data/create', [AdminDataController::class, 'create'])
            ->name('admin-data.create');

        Route::post('/admin-data', [AdminDataController::class, 'store'])
            ->name('admin-data.store');

        // DETAIL
        Route::get('/admin-data/{admin}', [AdminDataController::class, 'show'])
            ->name('admin-data.show');

        // EDIT
        Route::get('/admin-data/{admin}/edit', [AdminDataController::class, 'edit'])
            ->name('admin-data.edit');

        Route::put('/admin-data/{admin}', [AdminDataController::class, 'update'])
            ->name('admin-data.update');

        // DELETE
        Route::delete('/admin-data/{admin}', [AdminDataController::class, 'destroy'])
            ->name('admin-data.destroy');

        // ================= DATA SISWA =================
    
        // LIST
        Route::get('/siswa-data', [SiswaController::class, 'index'])
            ->name('siswa.index');

        // CREATE
        Route::get('/siswa-data/create', [SiswaController::class, 'create'])
            ->name('siswa.create');
        Route::post('/siswa-data', [SiswaController::class, 'store'])
            ->name('siswa.store');

        // DETAIL
        Route::get('/siswa-data/{siswa}', [SiswaController::class, 'show'])
            ->name('siswa.show');

        // EDIT
        Route::get('/siswa-data/{siswa}/edit', [SiswaController::class, 'edit'])
            ->name('siswa.edit');
        Route::put('/siswa-data/{siswa}', [SiswaController::class, 'update'])
            ->name('siswa.update');

        // DELETE
        Route::delete('/siswa-data/{siswa}', [SiswaController::class, 'destroy'])
            ->name('siswa.destroy');

        // ================= DATA GURU PEMBIMBING =================
    
        // LIST DATA GURU
        Route::get('/guru-data', [GuruPembimbingController::class, 'index'])
            ->name('guru.index');

        // FORM TAMBAH PROFIL GURU
        Route::get('/guru-data/create', [GuruPembimbingController::class, 'create'])
            ->name('guru.create');

        // SIMPAN PROFIL GURU
        Route::post('/guru-data', [GuruPembimbingController::class, 'store'])
            ->name('guru.store');

        // FORM EDIT PROFIL GURU
        Route::get('/guru-data/{guru}/edit', [GuruPembimbingController::class, 'edit'])
            ->name('guru.edit');

        // UPDATE PROFIL GURU
        Route::put('/guru-data/{guru}', [GuruPembimbingController::class, 'update'])
            ->name('guru.update');

        // HAPUS PROFIL GURU
        Route::delete('/guru-data/{guru}', [GuruPembimbingController::class, 'destroy'])
            ->name('guru.destroy');

        Route::get('/pembimbing-data', [PembimbingLapanganController::class, 'index'])
            ->name('pembimbing.index');

        Route::get('/pembimbing-data/create', [PembimbingLapanganController::class, 'create'])
            ->name('pembimbing.create');

        Route::post('/pembimbing-data', [PembimbingLapanganController::class, 'store'])
            ->name('pembimbing.store');

        Route::get('/pembimbing-data/{pembimbing}/edit', [PembimbingLapanganController::class, 'edit'])
            ->name('pembimbing.edit');

        Route::put('/pembimbing-data/{pembimbing}', [PembimbingLapanganController::class, 'update'])
            ->name('pembimbing.update');

        Route::delete('/pembimbing-data/{pembimbing}', [PembimbingLapanganController::class, 'destroy'])
            ->name('pembimbing.destroy');

        Route::get('/informasi-pkl', [InformasiPKLController::class, 'index'])
            ->name('informasi-pkl.index');

        Route::get('/informasi-pkl/create', [InformasiPKLController::class, 'create'])
            ->name('informasi-pkl.create');

        Route::post('/informasi-pkl', [InformasiPKLController::class, 'store'])
            ->name('informasi-pkl.store');

        Route::get('/informasi-pkl/{informasiPkl}/edit', [InformasiPKLController::class, 'edit'])
            ->name('informasi-pkl.edit');

        Route::put('/informasi-pkl/{informasiPkl}', [InformasiPKLController::class, 'update'])
            ->name('informasi-pkl.update');

        Route::delete('/informasi-pkl/{informasiPkl}', [InformasiPKLController::class, 'destroy'])
            ->name('informasi-pkl.destroy');

        Route::get('/tempat-pkl', [TempatPKLController::class, 'index'])
            ->name('tempat-pkl.index');

        Route::get('/tempat-pkl/create', [TempatPKLController::class, 'create'])
            ->name('tempat-pkl.create');

        Route::post('/tempat-pkl', [TempatPKLController::class, 'store'])
            ->name('tempat-pkl.store');

        Route::get('/tempat-pkl/{tempatPkl}/edit', [TempatPKLController::class, 'edit'])
            ->name('tempat-pkl.edit');

        Route::put('/tempat-pkl/{tempatPkl}', [TempatPKLController::class, 'update'])
            ->name('tempat-pkl.update');

        Route::delete('/tempat-pkl/{tempatPkl}', [TempatPKLController::class, 'destroy'])
            ->name('tempat-pkl.destroy');

        Route::resource('/periode', PeriodePklController::class)
            ->parameters(['periode' => 'periode'])
            ->names('periode');

        Route::resource('/penempatan', PenempatanPklController::class)
            ->names('penempatan');

        Route::post(
            '/penempatan/bulk-status',
            [PenempatanPklController::class, 'bulkUpdateStatus']
        )->name('penempatan.bulk-status');

        Route::get('/rekap-presensi', [RekapPresensiController::class, 'index'])
            ->name('rekap-presensi');

        Route::get('/rekap-laporan', [RekapLaporanController::class, 'index'])
            ->name('rekap-laporan');

        Route::get('/rekap-kpi', [RekapKPIController::class, 'index'])
            ->name('rekap-kpi.index');

        Route::get('/laporan-akhir', [LaporanAkhirController::class, 'index'])
            ->name('laporan-akhir.index');

        Route::get('/laporan-akhir/preview', [LaporanAkhirController::class, 'preview'])
            ->name('laporan-akhir.preview');

        Route::post('/laporan-akhir/preview', [LaporanAkhirController::class, 'preview'])
            ->name('laporan-akhir.preview');

        Route::post('/laporan-akhir/export', [LaporanAkhirController::class, 'exportPdf'])
            ->name('laporan-akhir.export');

        Route::get('/kpi-bobot', [KpiBobotController::class, 'index'])
            ->name('kpi-bobot');

        Route::post('/kpi-bobot', [KpiBobotController::class, 'update'])
            ->name('kpi-bobot.update');

        // ================= DATA & LAPORAN =================
    
        //Route::view('/tempat-pkl', 'dashboard.admin.tempat-pkl.index')->name('tempat-pkl');
        //Route::view('/penempatan', 'dashboard.admin.penempatan.index')->name('penempatan');
        //Route::view('/periode', 'dashboard.admin.periode.index')->name('periode');
        //Route::view('/informasi', 'dashboard.admin.informasi.index')->name('informasi');
        //Route::view('/rekap-presensi', 'dashboard.admin.rekap-presensi.index')->name('rekap-presensi');
        //Route::view('/rekap-laporan', 'dashboard.admin.rekap-laporan.index')->name('rekap-laporan');
        //Route::view('/rekap-kpi', 'dashboard.admin.rekap-kpi.index')->name('rekap-kpi');
        //Route::view('/laporan-akhir', 'dashboard.admin.laporan-akhir.index')->name('laporan-akhir');
    });

/*
|--------------------------------------------------------------------------
| SISWA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.active', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/profil', [ProfilController::class, 'index'])
            ->name('profil');

        Route::get('/riwayat-presensi', [RiwayatPresensiController::class, 'index'])
            ->name('riwayat-presensi');

        Route::get('/presensi', [PresensiController::class, 'index'])
            ->name('presensi');

        Route::post('/presensi/hadir', [PresensiController::class, 'hadir'])
            ->name('presensi.hadir');

        Route::post('/presensi/izin', [PresensiController::class, 'izin'])
            ->name('presensi.izin');

        Route::post('/presensi/libur', [PresensiController::class, 'Libur'])
            ->name('presensi.libur');

        Route::get('/laporan-kegiatan', [LaporanKegiatanController::class, 'index'])
            ->name('laporan-kegiatan.index');

        Route::post('/laporan-kegiatan', [LaporanKegiatanController::class, 'store'])
            ->name('laporan-kegiatan.store');

        Route::get('/riwayat-laporan', [RiwayatLaporanController::class, 'index'])
            ->name('riwayat-laporan');

        Route::get('/kpi', [KPIController::class, 'index'])
            ->name('kpi.index');

        Route::get('/evaluasi', [EvaluasiSiswaController::class, 'index'])
            ->name('evaluasi');
    });


/*
|--------------------------------------------------------------------------
| GURU PEMBIMBING ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.active', 'role:guru_pembimbing'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        Route::get('/dashboard', [GuruDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/siswa-bimbingan', [SiswaBimbinganController::class, 'index'])
            ->name('siswa-bimbingan.index');

        Route::get(
            '/siswa-bimbingan',
            [SiswaBimbinganController::class, 'index']
        )->name('siswa-bimbingan.index');

        Route::get(
            '/siswa-bimbingan/{penempatan}',
            [SiswaBimbinganController::class, 'show']
        )->name('siswa-bimbingan.show');

        Route::get(
            '/monitoring-presensi',
            [MonitoringPresensiController::class, 'index']
        )->name('monitoring-presensi.index');

        Route::get(
            '/validasi-laporan',
            [ValidasiLaporanController::class, 'index']
        )->name('validasi-laporan.index');

        Route::get(
            '/validasi-laporan/{laporan}',
            [ValidasiLaporanController::class, 'show']
        )->name('validasi-laporan.show');

        Route::post(
            '/validasi-laporan/{laporan}/validasi',
            [ValidasiLaporanController::class, 'validasi']
        )->name('validasi-laporan.validasi');

        Route::get(
            '/riwayat-validasi',
            [RiwayatValidasiController::class, 'index']
        )->name('riwayat-validasi.index');

        Route::get('/validasi-kpi', [ValidasiKPIController::class, 'index'])
            ->name('validasi-kpi.index');

        Route::post(
            '/validasi-kpi/{penempatan}/{tanggal}',
            [ValidasiKPIController::class, 'validateKpi']
        )->name('validasi-kpi.validate');

        Route::get(
            '/monitoring-kpi',
            [MonitoringKPIController::class, 'index']
        )->name('monitoring-kpi.index');

        Route::get(
            '/rekap-siswa',
            [RekapSiswaController::class, 'index']
        )->name('rekap-siswa.index');

        Route::post(
            '/rekap-siswa/catatan',
            [RekapSiswaController::class, 'storeCatatan']
        )->name('rekap-siswa.catatan');

        Route::get(
            'validasi-tempat',
            [ValidasiTempatController::class, 'index']
        )->name('validasi-tempat.index');

        Route::post(
            '/validasi-tempat/{id}',
            [ValidasiTempatController::class, 'validate']
        )->name('validasi-tempat.validate');

        Route::get(
            '/validasi-laporan-akhir',
            [ValidasiLaporanAkhirController::class, 'index']
        )->name('validasi-laporan-akhir.index');

        Route::post(
            '/validasi-laporan-akhir/{id}',
            [ValidasiLaporanAkhirController::class, 'validateLaporan']
        )->name('validasi-laporan-akhir.validate');

        Route::get(
            '/validasi-laporan-akhir/{id}/presensi',
            [ValidasiLaporanAkhirController::class, 'showPresensi']
        )->name('validasi-laporan-akhir.presensi');

        Route::get(
            '/validasi-laporan-akhir/{id}/nilai',
            [ValidasiLaporanAkhirController::class, 'showNilai']
        )->name('validasi-laporan-akhir.nilai');

        Route::get(
            '/validasi-laporan-akhir/{id}/laporan',
            [ValidasiLaporanAkhirController::class, 'showLaporan']
        )->name('validasi-laporan-akhir.laporan');
    });


/*
|--------------------------------------------------------------------------
| PEMBIMBING LAPANGAN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.active', 'role:pembimbing_lapangan'])
    ->prefix('pembimbing')
    ->name('pembimbing.')
    ->group(function () {

        Route::get('/dashboard', [PembimbingDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/siswa-pkl', [SiswaPKLController::class, 'index'])
            ->name('siswa-pkl.index');

        Route::get('/validasi-presensi', [ValidasiPresensiController::class, 'index'])
            ->name('validasi-presensi.index');

        Route::post('/validasi-presensi/{presensi}/terima', [ValidasiPresensiController::class, 'terima'])
            ->name('validasi-presensi.terima');

        Route::post('/validasi-presensi/{presensi}/tolak', [ValidasiPresensiController::class, 'tolak'])
            ->name('validasi-presensi.tolak');

        Route::get('/riwayat-presensi', [RiwayatPresensiPembimbingController::class, 'index'])
            ->name('riwayat-presensi.index');

        Route::get('/penilaian-kpi', [PenilaianKPIController::class, 'index'])
            ->name('penilaian-kpi.index');

        Route::post('/penilaian-kpi', [PenilaianKPIController::class, 'store'])
            ->name('penilaian-kpi.store');

        Route::get('/riwayat-kpi', [RiwayatKPIController::class, 'index'])
            ->name('riwayat-kpi.index');

        Route::get('/evaluasi', [EvaluasiController::class, 'index'])
            ->name('evaluasi.index');

        Route::post('/evaluasi', [EvaluasiController::class, 'store'])
            ->name('evaluasi.store');

    });

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
