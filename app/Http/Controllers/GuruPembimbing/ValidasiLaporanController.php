<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use App\Models\GuruPembimbing;
use App\Models\LaporanKegiatan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidasiLaporanController extends Controller
{
    public function index(Request $request)
    {
        $guru = GuruPembimbing::where('user_id', auth()->id())->firstOrFail();

        // 🔹 Query utama laporan (SEMUA DATA DULU)
        $query = LaporanKegiatan::with([
            'penempatanPkl.siswa'
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

        /* ================= FILTER TANGGAL (OPSIONAL) ================= */
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        /* ================= FILTER STATUS ================= */
        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        $laporan = $query
            ->orderBy('tanggal', 'desc')
            ->get();

        $laporan = $query->paginate(10)->withQueryString();

        /* ================= DROPDOWN SISWA ================= */
        $daftarSiswa = Siswa::whereHas('penempatanPkl', function ($q) use ($guru) {
                $q->where('guru_pembimbing_id', $guru->id)
                  ->where('status', 'aktif')
                  ->whereHas('siswa.user', fn($qq) => $qq->where('is_active', 1));
            })
            ->whereHas('user', fn($q) => $q->where('is_active', 1)) 
            ->orderBy('nama_lengkap')
            ->get();

        return view(
            'dashboard.guru-dashboard.validasi-laporan.index',
            compact('laporan', 'daftarSiswa')
        );
    }

    public function show($laporanId)
    {
        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        $laporan = LaporanKegiatan::with([
            'penempatanPkl.siswa'
        ])
            ->where('id', $laporanId)
            ->whereHas('penempatanPkl', function ($q) use ($guru) {
                $q->where('guru_pembimbing_id', $guru->id)
                ->whereHas('siswa.user', fn($qq) => $qq->active());
            })
            ->firstOrFail();

        return view('dashboard.guru-dashboard.validasi-laporan.show', compact('laporan'));
    }

    public function validasi(Request $request, $laporanId)
    {
        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'status_validasi' => 'required|in:diterima,ditolak',
            'catatan_validasi' => 'nullable|string'
        ]);

        $laporan = LaporanKegiatan::where('id', $laporanId)
            ->whereHas('penempatanPkl', function ($q) use ($guru) {
                $q->where('guru_pembimbing_id', $guru->id)
                ->whereHas('siswa.user', fn($qq) => $qq->active());
            })
            ->firstOrFail();

        $laporan->update([
            'status_validasi' => $request->status_validasi,
            'catatan_validasi' => $request->catatan_validasi,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return redirect()
            ->route('guru.validasi-laporan.index')
            ->with('success', 'Laporan berhasil divalidasi');
    }
}
