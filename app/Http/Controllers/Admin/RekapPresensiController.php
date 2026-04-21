<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\TempatPkl;
use App\Models\PeriodePkl;

class RekapPresensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Presensi::with([
            'penempatanPkl.siswa',
            'penempatanPkl.tempatPkl',
            'penempatanPkl.periodePkl'
        ]);

        // ================= FILTER =================

        if ($request->filled('nama_siswa')) {
            $query->whereHas('penempatanPkl.siswa', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama_siswa . '%');
            });
        }

        if ($request->filled('tempat_pkl_id')) {
            $query->whereHas('penempatanPkl', function ($q) use ($request) {
                $q->where('tempat_pkl_id', $request->tempat_pkl_id);
            });
        }

        if ($request->filled('periode_pkl_id')) {
            $query->whereHas('penempatanPkl', function ($q) use ($request) {
                $q->where('periode_pkl_id', $request->periode_pkl_id);
            });
        }

        if ($request->filled('status_validasi')) {
            $query->where('status_validasi', $request->status_validasi);
        }

        $presensi = $query
            ->latest('tanggal')
            ->paginate(15);

        // ================= DROPDOWN =================

        $daftarTempatPkl = TempatPkl::select('id', 'nama_perusahaan')
            ->orderBy('nama_perusahaan')
            ->get();

        $daftarPeriode = PeriodePkl::select('id', 'nama_periode')
            ->orderBy('nama_periode')
            ->get();

        return view('dashboard.admin.rekap-presensi.index', compact(
            'presensi',
            'daftarTempatPkl',
            'daftarPeriode'
        ));
    }

    public function show($id)
    {
        $detail = Presensi::with([
            'penempatanPkl.siswa',
            'penempatanPkl.tempatPkl'
        ])->findOrFail($id);

        return view('admin.rekap.presensi_detail', compact('detail'));
    }
}