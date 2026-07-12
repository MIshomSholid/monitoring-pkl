<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanKegiatan;
use App\Models\TempatPkl;
use App\Models\PeriodePkl;
use App\Models\Siswa;

class RekapLaporanController extends Controller
{
    public function index(Request $request)
    {
        // DATA FILTER
        $daftarTempatPkl = TempatPkl::orderBy('nama_perusahaan')->get();
        $daftarPeriode = PeriodePkl::orderBy('nama_periode')->get();

        // QUERY UTAMA (ELOQUENT)
        $laporan = LaporanKegiatan::with([
            'penempatanPkl.siswa',
            'penempatanPkl.tempatPkl',
            'penempatanPkl.periodePkl'
        ])

            // FILTER SISWA 
            ->when($request->filled('nama_siswa'), function ($q) use ($request) {
                $q->whereHas('penempatanPkl.siswa', function ($qq) use ($request) {
                    $qq->where('nama_lengkap', 'like', '%' . $request->nama_siswa . '%');
                });
            })

            // FILTER TEMPAT PKL
            ->when($request->filled('tempat_pkl_id'), function ($q) use ($request) {
                $q->whereHas('penempatanPkl', function ($qq) use ($request) {
                    $qq->where('tempat_pkl_id', $request->tempat_pkl_id);
                });
            })

            // FILTER PERIODE PKL
            ->when($request->filled('periode_pkl_id'), function ($q) use ($request) {
                $q->whereHas('penempatanPkl', function ($qq) use ($request) {
                    $qq->where('periode_pkl_id', $request->periode_pkl_id);
                });
            })

            // FILTER STATUS
            ->when($request->filled('status_validasi'), function ($q) use ($request) {
                $q->where('status_validasi', $request->status_validasi);
            })

            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('dashboard.admin.rekap-laporan.index', [
            'laporan' => $laporan,
            'daftarTempatPkl' => $daftarTempatPkl,
            'daftarPeriode' => $daftarPeriode,
        ]);
    }
}
