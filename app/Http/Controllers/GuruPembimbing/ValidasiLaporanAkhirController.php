<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\PenempatanPkl;
use App\Models\LaporanAkhirPkl;

class ValidasiLaporanAkhirController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->guruPembimbing;

        $penempatan = PenempatanPkl::withoutGlobalScope('aktif') // 
            ->with([
                'siswa',
                'tempatPkl',
                'periodePkl',
                'laporanAkhir'
            ])
            ->where('guru_pembimbing_id', $guru->id)
            ->where('status', 'aktif') 
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->get();

        return view(
            'dashboard.guru-dashboard.validasi-laporan-akhir.index',
            compact('penempatan')
        );
    }

    public function validateLaporan(Request $request, $id)
    {
        $request->validate([
            'status_validasi' => 'required|in:diterima,ditolak',
            'catatan_validasi' => 'nullable|string'
        ]);

        $penempatan = $this->getPenempatanByGuru($id);

        LaporanAkhirPkl::updateOrCreate(
            ['penempatan_pkl_id' => $penempatan->id],
            [
                'status_validasi' => $request->status_validasi,
                'catatan_validasi' => $request->catatan_validasi,
                'validated_by' => Auth::id(),
                'validated_at' => now()
            ]
        );

        return back()->with(
            'success',
            'Validasi laporan akhir berhasil disimpan.'
        );
    }

    public function showPresensi($id)
    {
        $penempatan = $this->getPenempatanByGuru($id)
            ->load([
                'siswa',
                'tempatPkl',
                'presensi'
            ]);

        return view(
            'dashboard.guru-dashboard.validasi-laporan-akhir.show-presensi',
            compact('penempatan')
        );
    }

    public function showNilai($id)
    {
        $penempatan = $this->getPenempatanByGuru($id)
            ->load([
                'siswa',
                'tempatPkl',
                'penilaianKpi.indikator.kategori'
            ]);

        return view(
            'dashboard.guru-dashboard.validasi-laporan-akhir.show-nilai',
            compact('penempatan')
        );
    }

    public function showLaporan($id)
    {
        $penempatan = $this->getPenempatanByGuru($id)
            ->load([
                'siswa',
                'tempatPkl',
                'laporanKegiatan'
            ]);

        return view(
            'dashboard.guru-dashboard.validasi-laporan-akhir.show-laporan',
            compact('penempatan')
        );
    }

    private function getPenempatanByGuru($id)
    {
        return PenempatanPkl::withoutGlobalScope('aktif') 
            ->where('id', $id)
            ->where('guru_pembimbing_id', Auth::user()->guruPembimbing->id)
            ->where('status', 'aktif') 
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->firstOrFail();
    }
}