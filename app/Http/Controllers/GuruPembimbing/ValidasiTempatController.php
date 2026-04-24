<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PenempatanPkl;

class ValidasiTempatController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->guruPembimbing;

        $data = PenempatanPkl::withoutGlobalScope('aktif')
            ->with([
                'siswa',
                'tempat',
                'pembimbingLapangan',
                'periode'
            ])
            ->where('guru_pembimbing_id', $guru->id)
            ->latest()
            ->get();

        return view('dashboard.guru-dashboard.validasi-tempat.index', compact('data'));
    }

    public function validate(Request $request, $id)
    {
        $request->validate([
            'status_validasi' => 'required|in:menunggu,diterima,ditolak'
        ]);

        $penempatan = PenempatanPkl::withoutGlobalScope('aktif')->findOrFail($id);

        // 🔥 LOGIKA VALIDASI
        if ($request->status_validasi === 'diterima') {
            $penempatan->update([
                'status_validasi' => 'diterima',
                'status' => $penempatan->status_pengajuan ?? 'aktif',
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);
        } elseif ($request->status_validasi === 'ditolak') {
            $penempatan->update([
                'status_validasi' => 'ditolak',
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);
        } else {
            // kalau balik ke menunggu
            $penempatan->update([
                'status_validasi' => 'menunggu',
                'validated_by' => null,
                'validated_at' => null,
            ]);
        }

        return back()->with('success', 'Validasi berhasil diproses.');
    }
}