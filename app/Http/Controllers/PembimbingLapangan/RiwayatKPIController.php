<?php

namespace App\Http\Controllers\PembimbingLapangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PenilaianKpi;
use App\Models\PenempatanPkl;

class RiwayatKPIController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // daftar siswa yang dibimbing (dropdown)
        $penempatanList = PenempatanPkl::with(['siswa', 'tempatPkl'])
            ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'aktif')
            ->where('status_validasi', 'diterima')
            ->get();

        $data = collect();
        $tanggalList = collect();
        $tanggalAktif = null;
        $prev = null;
        $next = null;

        if ($request->filled('penempatan_pkl_id')) {

            // ambil semua tanggal KPI
            $tanggalList = PenilaianKpi::where('input_by', $userId)
                ->where('penempatan_pkl_id', $request->penempatan_pkl_id)
                ->selectRaw('DATE(periode_penilaian) as tanggal')
                ->distinct()
                ->orderBy('tanggal', 'desc')
                ->pluck('tanggal');

            if ($tanggalList->count()) {

                // tentukan tanggal aktif
                $tanggalAktif = $request->tanggal ?? $tanggalList->first();

                $index = $tanggalList->search($tanggalAktif);

                $prev = $tanggalList[$index + 1] ?? null;
                $next = $tanggalList[$index - 1] ?? null;

                // ambil KPI tanggal tersebut
                $data = PenilaianKpi::with([
                    'penempatanPkl.siswa',
                    'penempatanPkl.tempatPkl',
                    'indikator.kategori'
                ])
                    ->where('input_by', $userId)
                    ->where('penempatan_pkl_id', $request->penempatan_pkl_id)
                    ->whereDate('periode_penilaian', $tanggalAktif)
                    ->get();
            }
        }

        return view(
            'dashboard.pembimbing-dashboard.riwayat-kpi.index',
            compact(
                'penempatanList',
                'data',
                'tanggalAktif',
                'prev',
                'next'
            )
        );
    }
}