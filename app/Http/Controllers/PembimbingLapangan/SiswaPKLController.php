<?php

namespace App\Http\Controllers\PembimbingLapangan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PenempatanPkl;

class SiswaPKLController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $data = PenempatanPkl::withoutGlobalScope('aktif')
            ->with([
                'siswa',
                'periodePkl',
                'guruPembimbing'
            ])
            ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'aktif')
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'dashboard.pembimbing-dashboard.siswa-pkl.index',
            compact('data')
        );
    }
}
