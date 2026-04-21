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

        // siswa bimbingan
        $penempatanList = PenempatanPkl::with(['siswa', 'tempat'])
            ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'aktif')
            ->get();

        $data = collect();

        if ($request->filled('penempatan_pkl_id')) {
            $data = CatatanEvaluasi::with(['penempatan.siswa', 'penempatan.tempat'])
                ->where('penempatan_pkl_id', $request->penempatan_pkl_id)
                ->where('kategori', 'pembimbing_lapangan')
                ->orderBy('tanggal', 'desc')
                ->paginate(10)
                ->withQueryString();
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

        CatatanEvaluasi::create([
            'penempatan_pkl_id' => $request->penempatan_pkl_id,
            'kategori'          => 'pembimbing_lapangan',
            'catatan'           => $request->catatan,
            'tanggal'           => now()->toDateString(),
            'created_by'        => Auth::id(),
        ]);

        return back()->with('success', 'Catatan dan evaluasi berhasil disimpan.');
    }
}
