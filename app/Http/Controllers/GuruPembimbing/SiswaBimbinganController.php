<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use App\Models\GuruPembimbing;
use App\Models\PenempatanPkl;
use Illuminate\Support\Facades\Auth;

class SiswaBimbinganController extends Controller
{
    public function index()
    {
        // Ambil data guru pembimbing berdasarkan user login
        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        // Ambil siswa yang dibimbing guru tersebut
        $siswaBimbingan = PenempatanPkl::withoutGlobalScope('aktif')
            ->with([
                'siswa',
                'tempat',
                'periode',
                'pembimbingLapangan'
            ])
            ->where('guru_pembimbing_id', $guru->id)
            ->where('status', 'aktif')
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->get();

        return view('dashboard.guru-dashboard.siswa-bimbingan.index', compact('siswaBimbingan'));
    }

    public function show($penempatanId)
    {
        $guru = GuruPembimbing::where('user_id', auth()->id())->firstOrFail();

        $penempatan = PenempatanPkl::withoutGlobalScope('aktif')
            ->with([
                'siswa',
                'tempat',
                'periode',
                'pembimbingLapangan'
            ])
            ->where('id', $penempatanId)
            ->where('guru_pembimbing_id', $guru->id)
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->firstOrFail();

        return view('dashboard.guru-dashboard.siswa-bimbingan.show', compact('penempatan'));
    }

}
