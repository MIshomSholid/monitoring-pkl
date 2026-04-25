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

        $penempatanList = PenempatanPkl::withoutGlobalScope('aktif')
            ->with(['siswa', 'tempatPkl'])
            ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'aktif')
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->orderBy('created_at', 'desc')
            ->get();

        $data = collect();
        $tanggalList = collect();
        $tanggalAktif = null;
        $prev = null;
        $next = null;

        if ($request->filled('penempatan_pkl_id')) {

            $penempatan = PenempatanPkl::withoutGlobalScope('aktif')
                ->where('id', $request->penempatan_pkl_id)
                ->where('status', 'aktif')
                ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->first();

            if ($penempatan) {

                // ambil semua tanggal KPI
                $tanggalList = PenilaianKpi::where('input_by', $userId)
                    ->where('penempatan_pkl_id', $penempatan->id)
                    ->selectRaw('DATE(periode_penilaian) as tanggal')
                    ->distinct()
                    ->orderBy('tanggal', 'desc')
                    ->pluck('tanggal');

                if ($tanggalList->count()) {

                    $tanggalAktif = $request->tanggal ?? $tanggalList->first();

                    $index = $tanggalList->search($tanggalAktif);

                    $prev = $tanggalList[$index + 1] ?? null;
                    $next = $tanggalList[$index - 1] ?? null;

                    // ambil KPI
                    $data = PenilaianKpi::with([
                        'penempatanPkl.siswa',
                        'penempatanPkl.tempatPkl',
                        'indikator.kategori'
                    ])
                        ->where('input_by', $userId)
                        ->where('penempatan_pkl_id', $penempatan->id)
                        ->whereDate('periode_penilaian', $tanggalAktif)
                        ->get();
                }
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