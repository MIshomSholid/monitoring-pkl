<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GuruPembimbing;
use App\Models\PenilaianKpi;

class ValidasiKPIController extends Controller
{
    public function index(Request $request)
    {
        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        $query = PenilaianKpi::with([
            'penempatanPkl.siswa',
            'indikator.kategori'
        ])
            ->whereHas('penempatanPkl', function ($q) use ($guru) {
                $q->where('guru_pembimbing_id', $guru->id)
                    ->where('status', 'aktif')
                    ->whereHas('siswa.user', fn($qq) => $qq->where('is_active', 1));
            });

        /* ================= FILTER SISWA ================= */
        if ($request->filled('siswa_id')) {
            $query->whereHas('penempatanPkl', function ($q) use ($request) {
                $q->where('siswa_id', $request->siswa_id);
            });
        }

        /* ================= AMBIL SEMUA TANGGAL KPI ================= */
        $tanggalList = (clone $query)
            ->select('periode_penilaian')
            ->distinct()
            ->orderBy('periode_penilaian', 'desc')
            ->pluck('periode_penilaian')
            ->values();

        /* ================= TANGGAL AKTIF ================= */
        $tanggalAktif = $request->tanggal ?? $tanggalList->first();

        if (!$tanggalAktif) {
            $data = collect();
        } else {
            $data = $query
                ->whereDate('periode_penilaian', $tanggalAktif)
                ->orderBy('periode_penilaian', 'desc')
                ->get()
                ->groupBy('penempatan_pkl_id');
        }

        // /* ================= DATA KPI PER TANGGAL ================= */
        // $data = $query
        //     ->whereDate('periode_penilaian', $tanggalAktif)
        //     ->orderBy('periode_penilaian', 'desc')
        //     ->get()
        //     ->groupBy('penempatan_pkl_id');

        /* ================= NAVIGASI PREV / NEXT ================= */
        $index = $tanggalList->search($tanggalAktif);

        $prev = null;
        $next = null;

        if ($index !== false) {
            $prev = $tanggalList[$index + 1] ?? null;
            $next = $index > 0 ? $tanggalList[$index - 1] : null;
        }

        /* ================= DROPDOWN SISWA ================= */
        $daftarSiswa = $guru->penempatanPkl()
            ->with('siswa')
            ->where('status', 'aktif')
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->get()
            ->pluck('siswa')
            ->unique('id')
            ->values();

        return view(
            'dashboard.guru-dashboard.validasi-kpi.index',
            compact(
                'data',
                'daftarSiswa',
                'tanggalAktif',
                'prev',
                'next'
            )
        );
    }

    public function validateKpi(Request $request, $penempatanPklId, $tanggal)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        $updated = PenilaianKpi::whereHas('penempatanPkl', function ($q) use ($penempatanPklId, $guru) {
            $q->where('id', $penempatanPklId)
                ->where('guru_pembimbing_id', $guru->id); // ✅ FIX DISINI
        })
            ->whereDate('periode_penilaian', $tanggal)
            ->update([
                'status_validasi' => $request->status,
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);
        
        dd($updated);

        return back()->with('success', 'Penilaian KPI berhasil divalidasi.');
    }
}