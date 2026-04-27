<?php

namespace App\Http\Controllers\PembimbingLapangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PenempatanPkl;
use App\Models\CatatanEvaluasi;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Ambil semua penempatan (biarkan global scope jalan)
        $penempatan = PenempatanPkl::with(['siswa', 'tempat'])
            ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get();

        // FILTER: hanya yang aktif hari ini
        $penempatanAktif = $penempatan->filter(function ($p) {
            return $p->isAktifHariIni();
        });

        $isDalamMasaPkl = $penempatanAktif->isNotEmpty();

        $data = collect();

        if ($request->filled('penempatan_pkl_id') && $isDalamMasaPkl) {

            $penempatanDipilih = $penempatanAktif
                ->where('id', $request->penempatan_pkl_id)
                ->first();

            if ($penempatanDipilih) {
                $data = CatatanEvaluasi::with(['penempatan.siswa', 'penempatan.tempat'])
                    ->where('penempatan_pkl_id', $penempatanDipilih->id)
                    ->where('kategori', 'pembimbing_lapangan')
                    ->orderBy('tanggal', 'desc')
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        return view(
            'dashboard.pembimbing-dashboard.evaluasi.index',
            compact(
                'penempatanAktif',
                'data',
                'isDalamMasaPkl'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'penempatan_pkl_id' => 'required|exists:penempatan_pkl,id',
            'catatan' => 'required|string',
        ]);

        $penempatan = PenempatanPkl::findOrFail($request->penempatan_pkl_id);

        if (!$penempatan->isAktifHariIni()) {
            return back()->withErrors([
                'penempatan_pkl_id' => 'Evaluasi hanya bisa dilakukan saat masa PKL berlangsung.'
            ]);
        }

        $penempatan = PenempatanPkl::withoutGlobalScope('aktif')
            ->where('id', $request->penempatan_pkl_id)
            ->where('status', 'aktif')
            ->whereHas('pembimbingLapangan', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->firstOrFail();

        CatatanEvaluasi::create([
            'penempatan_pkl_id' => $penempatan->id,
            'kategori' => 'pembimbing_lapangan',
            'catatan' => $request->catatan,
            'tanggal' => now()->toDateString(),
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Catatan dan evaluasi berhasil disimpan.');
    }
}