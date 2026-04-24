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
            'status_validasi' => 'required|in:diterima,ditolak'
        ]);

        $penempatan = PenempatanPkl::withoutGlobalScope('aktif')->findOrFail($id);

        if (!$penempatan->status_pengajuan) {
            return back()->with('error', 'Tidak ada pengajuan yang perlu divalidasi.');
        }

        if ($penempatan->status_validasi === 'diterima') {
            return back()->with('error', 'Data sudah disetujui dan tidak dapat diubah.');
        }

        if ($request->status_validasi === 'diterima') {

            $penempatan->update([
                'status_validasi' => 'diterima',
                'status' => $penempatan->status_pengajuan ?? $penempatan->status, // ✅ FIX
                'status_pengajuan' => null,
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);

        } elseif ($request->status_validasi === 'ditolak') {

            $penempatan->update([
                'status_validasi' => 'ditolak',
                'status_pengajuan' => null,
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);
        }

        return back()->with('success', 'Validasi berhasil diproses.');
    }
}