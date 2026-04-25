<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use App\Models\GuruPembimbing;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringPresensiController extends Controller
{
    public function index(Request $request)
    {
        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        $query = Presensi::with([
            'penempatanPkl.siswa',
            'penempatanPkl.tempatPkl'
        ])
            ->whereHas('penempatanPkl', function ($q) use ($guru) {
                $q->where('guru_pembimbing_id', $guru->id);
                $q->where('status', 'aktif');
                $q->where('status_validasi', 'diterima');
            })

            //FILTER AKUN SISWA YANG NON-AKTIF
            ->whereHas('penempatanPkl.siswa.user', fn($q) => $q->active())

            // FILTER SISWA
            ->when($request->filled('siswa_id'), function ($q) use ($request) {
                $q->whereHas('penempatanPkl', function ($qq) use ($request) {
                    $qq->where('siswa_id', $request->siswa_id);
                });
            })

            // FILTER TANGGAL
            ->when($request->filled('tanggal'), function ($q) use ($request) {
                $q->whereDate('tanggal', $request->tanggal);
            })

            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_presensi', 'asc');

        $presensi = $query->paginate(10)->withQueryString();

        // Dropdown siswa
        $daftarSiswa = Siswa::whereHas('penempatanPkl', function ($q) use ($guru) {
            $q->where('guru_pembimbing_id', $guru->id)
                ->where('status_validasi', 'diterima');
        })
            ->whereHas('user', fn($q) => $q->active())
            ->orderBy('nama_lengkap')
            ->get();

        return view(
            'dashboard.guru-dashboard.monitoring-presensi.index',
            [
                'presensi' => $presensi,
                'daftarSiswa' => $daftarSiswa,
            ]
        );
    }
}