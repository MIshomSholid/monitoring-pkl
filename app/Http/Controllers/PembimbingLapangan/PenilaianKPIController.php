<?php

namespace App\Http\Controllers\PembimbingLapangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PenempatanPkl;
use App\Models\KpiIndikator;
use App\Models\PenilaianKpi;

class PenilaianKPIController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $tanggal = now()->toDateString();
        $today = now()->toDateString();

        // Semua siswa bimbingan
        $penempatan = PenempatanPkl::withoutGlobalScope('aktif')
            ->with(['siswa', 'tempat', 'periodePkl'])
            ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'aktif')
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->get();

        $penempatanAktifPkl = $penempatan->filter(function ($p) use ($today) {
            if (!$p->periodePkl)
                return false;

            return $today >= $p->periodePkl->tanggal_mulai &&
                $today <= $p->periodePkl->tanggal_selesai;
        });

        $today = now()->toDateString();

        $isDalamMasaPkl = $penempatan->contains(function ($p) use ($today) {

            if (!$p->periodePkl)
                return false;

            return $today >= $p->periodePkl->tanggal_mulai &&
                $today <= $p->periodePkl->tanggal_selesai;
        });

        // ambil siswa yang SUDAH DINILAI HARI INI
        $dinilaiHariIni = PenilaianKpi::whereDate('periode_penilaian', $tanggal)
            ->whereIn(
                'penempatan_pkl_id',
                $penempatan->pluck('id')
            )
            ->pluck('penempatan_pkl_id')
            ->unique()
            ->toArray();

        return view(
            'dashboard.pembimbing-dashboard.penilaian-kpi.index',
            compact(
                'penempatan',
                'penempatanAktifPkl',
                'dinilaiHariIni',
                'isDalamMasaPkl'
            )
        );
    }

    public function store(Request $request)
    {
        /* ================= VALIDASI ================= */
        $request->validate([
            'penempatan_pkl_id' => 'required|exists:penempatan_pkl,id',

            // ASPEK TEKNIS (OPSIONAL)
            'teknis.nama' => 'nullable|string',
            'teknis.nilai' => 'nullable|numeric|min:0|max:100',

            // ASPEK NON TEKNIS (WAJIB)
            'non_teknis' => 'required|array',
            'non_teknis.*' => 'required|numeric|min:0|max:100',
        ]);

        $penempatanId = $request->penempatan_pkl_id;
        $userId = Auth::id();
        $tanggal = now()->toDateString();

        /* ================= AMBIL PENEMPATAN + PERIODE ================= */
        $penempatan = PenempatanPkl::with('periodePkl')->findOrFail($penempatanId);

        /* ================= CEK MASA PKL ================= */
        $today = now()->toDateString();
        $mulai = $penempatan->periodePkl->tanggal_mulai ?? null;
        $selesai = $penempatan->periodePkl->tanggal_selesai ?? null;

        if (!$mulai || !$selesai || $today < $mulai || $today > $selesai) {
            return back()->withErrors([
                'penempatan_pkl_id' => 'Penilaian KPI hanya bisa dilakukan saat masa PKL berlangsung.'
            ]);
        }

        /* ================= CEK VALIDASI PEMBIMBING LAPANGAN & STATUS AKTIF ================= */
        $valid = PenempatanPkl::where('id', $penempatanId)
            ->where('status', 'aktif')
            ->whereHas('pembimbingLapangan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->exists();

        if (!$valid) {
            abort(403, 'Akses ditolak');
        }

        /* ================= CEK 1 HARI 1 KALI ================= */
        $alreadyExist = PenilaianKpi::where('penempatan_pkl_id', $penempatanId)
            ->whereDate('periode_penilaian', $tanggal)
            ->exists();

        if ($alreadyExist) {
            return back()->withErrors([
                'penempatan_pkl_id' => 'Penilaian KPI untuk siswa ini hari ini sudah dilakukan.'
            ]);
        }

        /* ================= ASPEK TEKNIS (JIKA ADA) ================= */
        if (
            filled($request->teknis['nama'] ?? null) &&
            filled($request->teknis['nilai'] ?? null)
        ) {
            $indikatorTeknis = KpiIndikator::firstOrCreate(
                ['nama_indikator' => $request->teknis['nama']],
                [
                    'kpi_kategori_id' => 1, // Aspek Teknis
                    'bobot_persen' => 0
                ]
            );

            PenilaianKpi::create([
                'penempatan_pkl_id' => $penempatanId,
                'kpi_indikator_id' => $indikatorTeknis->id,
                'nilai' => $request->teknis['nilai'],
                'periode_penilaian' => $tanggal,
                'input_by' => $userId,
                'status_validasi' => 'menunggu',
            ]);
        }

        /* ================= ASPEK NON TEKNIS ================= */
        foreach ($request->non_teknis as $nama => $nilai) {

            $indikator = KpiIndikator::firstOrCreate(
                ['nama_indikator' => $nama],
                [
                    'kpi_kategori_id' => 2, // Non Teknis
                    'bobot_persen' => 0
                ]
            );

            PenilaianKpi::create([
                'penempatan_pkl_id' => $penempatanId,
                'kpi_indikator_id' => $indikator->id,
                'nilai' => $nilai,
                'periode_penilaian' => $tanggal,
                'input_by' => $userId,
                'status_validasi' => 'menunggu',
            ]);
        }

        return redirect()
            ->route('pembimbing.penilaian-kpi.index')
            ->with('success', 'Penilaian KPI berhasil disimpan dan menunggu validasi Guru Pembimbing.');
    }
}
