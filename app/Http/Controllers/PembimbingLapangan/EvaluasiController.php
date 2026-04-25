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

        $penempatanList = PenempatanPkl::withoutGlobalScope('aktif')
            ->with(['siswa', 'tempat'])
            ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'aktif')
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->orderBy('created_at', 'desc')
            ->get();

        $data = collect();

        if ($request->filled('penempatan_pkl_id')) {

            $penempatan = PenempatanPkl::withoutGlobalScope('aktif')
                ->where('id', $request->penempatan_pkl_id)
                ->where('status', 'aktif')
                ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->first();

            if ($penempatan) {
                $data = CatatanEvaluasi::with(['penempatan.siswa', 'penempatan.tempat'])
                    ->where('penempatan_pkl_id', $penempatan->id)
                    ->where('kategori', 'pembimbing_lapangan')
                    ->orderBy('tanggal', 'desc')
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        return view(
            'dashboard.pembimbing-dashboard.evaluasi.index',
            compact('penempatanList', 'data')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'penempatan_pkl_id' => 'required|exists:penempatan_pkl,id',
            'catatan'           => 'required|string',
        ]);

        $penempatan = PenempatanPkl::withoutGlobalScope('aktif')
            ->where('id', $request->penempatan_pkl_id)
            ->where('status', 'aktif')
            ->whereHas('pembimbingLapangan', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->firstOrFail();

        CatatanEvaluasi::create([
            'penempatan_pkl_id' => $penempatan->id,
            'kategori'          => 'pembimbing_lapangan',
            'catatan'           => $request->catatan,
            'tanggal'           => now()->toDateString(),
            'created_by'        => Auth::id(),
        ]);

        return back()->with('success', 'Catatan dan evaluasi berhasil disimpan.');
    }
}