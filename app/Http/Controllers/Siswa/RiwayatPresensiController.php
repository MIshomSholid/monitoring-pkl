<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Presensi;
use Illuminate\Pagination\LengthAwarePaginator;

class RiwayatPresensiController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        $penempatan = $siswa->penempatanAktif;

        if (!$penempatan) {
            return view('dashboard.siswa-dashboard.riwayat-presensi.index', [
                'presensi' => new LengthAwarePaginator([], 0, 10)
            ]);
        }

        // WAJIB PAKAI MODEL + with()
        $presensi = Presensi::with('penempatanPkl.tempatPkl')
            ->where('penempatan_pkl_id', $penempatan->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view(
            'dashboard.siswa-dashboard.riwayat-presensi.index',
            compact('presensi')
        );
    }
}
