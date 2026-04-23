<?php

namespace App\Http\Controllers\PembimbingLapangan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Presensi;

class RiwayatPresensiPembimbingController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Presensi::with([
            'penempatanPkl.siswa',
            'penempatanPkl.tempat'
        ])
            ->whereIn('status_validasi', ['diterima', 'ditolak'])
            ->whereHas('penempatanPkl', function ($q) use ($userId) {
                $q->where('status', 'aktif') // 🔥 WAJIB
                    ->whereHas('pembimbingLapangan', function ($qq) use ($userId) {
                        $qq->where('user_id', $userId);
                    });
            });

        /* ================= FILTER SISWA (DROPDOWN) ================= */
        if ($request->filled('siswa_id')) {
            $query->whereHas('penempatanPkl', function ($q) use ($request) {
                $q->where('siswa_id', $request->siswa_id);
            });
        }

        /* ================= FILTER JENIS ================= */
        if ($request->filled('jenis')) {
            $query->where('jenis_presensi', $request->jenis);
        }

        /* ================= FILTER STATUS ================= */
        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        /* ================= FILTER TANGGAL (OPSIONAL) ================= */
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->withQueryString();

        /* ================= DROPDOWN SISWA ================= */
        $daftarSiswa = \App\Models\Siswa::whereHas('penempatanPkl', function ($q) use ($userId) {
            $q->where('status', 'aktif')
            ->where('status_validasi', 'diterima')
                ->whereHas('pembimbingLapangan', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId);
                });
        })
            ->orderBy('nama_lengkap')
            ->get();

        return view(
            'dashboard.pembimbing-dashboard.riwayat-presensi.index',
            compact('data', 'daftarSiswa')
        );
    }
}
