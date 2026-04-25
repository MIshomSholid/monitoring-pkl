<?php

namespace App\Http\Controllers\PembimbingLapangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Presensi;
use App\Models\PeriodePkl;

class ValidasiPresensiController extends Controller
{
    /**
     * Daftar presensi yang menunggu validasi
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Presensi::with([
            'penempatanPkl.siswa',
            'penempatanPkl.tempat'
        ])
            ->where('status_validasi', 'menunggu')
            ->where('jenis_presensi', '!=', 'alpha')
            ->whereHas('penempatanPkl', function ($q) use ($userId) {
                $q->withoutGlobalScope('aktif')
                    ->where('status', 'aktif')
                    ->whereHas('pembimbingLapangan', function ($qq) use ($userId) {
                        $qq->where('user_id', $userId);
                    });
            });

        /* ================= FILTER SISWA (DROPDOWN) ================= */
        if ($request->filled('siswa_id')) {
            $query->whereHas('penempatanPkl', function ($q) use ($request) {
                $q->where('siswa_id', $request->siswa_id);
            });
        }

        /* ================= FILTER TANGGAL (OPSIONAL) ================= */
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->get();

        /* ================= DROPDOWN SISWA ================= */
        $daftarSiswa = \App\Models\Siswa::whereHas('penempatanPkl', function ($q) use ($userId) {
            $q->withoutGlobalScope('aktif')
                ->where('status', 'aktif')
                ->whereHas('pembimbingLapangan', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId);
                });
        })
            ->orderBy('nama_lengkap')
            ->get();

        return view(
            'dashboard.pembimbing-dashboard.validasi-presensi.index',
            compact('data', 'daftarSiswa')
        );
    }

    /**
     * Terima presensi
     */
    public function terima(Presensi $presensi)
    {
        if ($presensi->jenis_presensi === 'alpha') {
            abort(403, 'Presensi alpha tidak dapat divalidasi.');
        }

        if ($presensi->penempatanPkl->pembimbingLapangan->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($presensi->penempatanPkl->status !== 'aktif') {
            return back()->with('error', 'PKL sudah tidak aktif.');
        }

        $presensi->update([
            'status_validasi' => 'diterima',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'alasan_penolakan' => null,
        ]);

        // REALTIME NOTIFIKASI VIA WEBSOCKET
        Http::post(env('REALTIME_URL') . '/broadcast', [
            'event' => 'presensi.validated',
            'data' => [
                'id' => $presensi->id,
                'pembimbing_user_id' => Auth::id(),
                'guru_user_id' => $presensi->penempatanPkl->guruPembimbing->user_id ?? null,
                'siswa_user_id' => $presensi->penempatanPkl->siswa->user_id ?? null,
            ]
        ]);

        return back()->with('success', 'Presensi berhasil diterima.');
    }

    /**
     * Tolak presensi
     */
    public function tolak(Request $request, Presensi $presensi)
    {
        if ($presensi->jenis_presensi === 'alpha') {
            abort(403, 'Presensi alpha tidak dapat divalidasi.');
        }

        if ($presensi->penempatanPkl->pembimbingLapangan->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:255',
        ]);

        $presensi->update([
            'status_validasi' => 'ditolak',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        // REALTIME NOTIFIKASI VIA WEBSOCKET
        Http::post(env('REALTIME_URL') . '/broadcast', [
            'event' => 'presensi.validated',
            'data' => [
                'id' => $presensi->id,
                'pembimbing_user_id' => Auth::id(),
                'guru_user_id' => $presensi->penempatanPkl->guruPembimbing->user_id ?? null,
                'siswa_user_id' => $presensi->penempatanPkl->siswa->user_id ?? null,
            ]
        ]);

        return back()->with('success', 'Presensi berhasil ditolak.');
    }
}
