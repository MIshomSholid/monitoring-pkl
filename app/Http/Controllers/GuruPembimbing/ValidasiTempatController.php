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
            ->get();

        // ================= MENUNGGU =================
        $menunggu = $data->where('status_validasi', 'menunggu');

        // ================= AKTIF (SUDAH DISETUJUI) =================
        $aktif = $data->where('status', 'aktif')
            ->where('status_validasi', 'diterima');

        // ================= NONAKTIF (SUDAH DISETUJUI) =================
        $nonaktif = $data
            ->where('status', 'nonaktif')
            ->where('status_validasi', 'diterima');

        // ================= RIWAYAT =================
        $riwayat = $data->whereIn('status_validasi', ['diterima', 'ditolak']);

        return view('dashboard.guru-dashboard.validasi-tempat.index', compact(
            'menunggu',
            'aktif',
            'nonaktif',
            'riwayat'
        ));
    }

    public function validate(Request $request, $id)
    {
        $request->validate([
            'status_validasi' => 'required|in:diterima,ditolak'
        ]);

        $penempatan = PenempatanPkl::withoutGlobalScope('aktif')->findOrFail($id);

        // LOGIKA UTAMA
        if ($request->status_validasi === 'diterima') {
            $penempatan->update([
                'status_validasi' => 'diterima',
                'status' => $penempatan->status_pengajuan ?? 'aktif', // ambil dari pengajuan
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);
        } else {
            $penempatan->update([
                'status_validasi' => 'ditolak',
                // ❗ status tidak berubah
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);
        }

        return back()->with('success', 'Validasi berhasil diproses.');
    }
}