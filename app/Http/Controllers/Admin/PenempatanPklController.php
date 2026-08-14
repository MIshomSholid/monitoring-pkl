<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Models\{
    PenempatanPkl,
    Siswa,
    TempatPkl,
    PeriodePkl,
    GuruPembimbing,
    PembimbingLapangan
};

class PenempatanPklController extends Controller
{
    // LIST PENEMPATAN PKL
    public function index(Request $request)
    {
        $query = PenempatanPkl::withoutGlobalScope('aktif')
            ->with([
                'siswa',
                'tempat',
                'periode',
                'guru',
                'pembimbingLapangan'
            ]);

        // FILTER PERIODE

        if (!$request->has('periode_id')) {

            // Halaman pertama dibuka
            $periodeAktif = PeriodePkl::where('is_active', true)->first();

            if ($periodeAktif) {
                $query->where('periode_pkl_id', $periodeAktif->id);
            }

        } elseif ($request->periode_id != 'all') {

            // Admin memilih periode tertentu
            $query->where('periode_pkl_id', $request->periode_id);

        }

        // Jika periode_id = all maka tampil semua data

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'siswa',
                    fn($qs) =>
                    $qs->where('nama_lengkap', 'like', "%{$search}%")
                )
                    ->orWhereHas(
                        'tempat',
                        fn($qt) =>
                        $qt->where('nama_perusahaan', 'like', "%{$search}%")
                    )
                    ->orWhereHas(
                        'guru',
                        fn($qg) =>
                        $qg->where('nama_lengkap', 'like', "%{$search}%")
                    );
            });
        }

        $penempatan = $query->latest()->get();
        $periode = PeriodePkl::orderBy('tanggal_mulai', 'desc')->get();

        return view('dashboard.admin.penempatan.index', compact('penempatan', 'periode'));
    }

    // FORM TAMBAH
    public function create()
    {
        $siswaTerpakai = PenempatanPkl::withoutGlobalScope('aktif')
            ->whereIn('status_validasi', ['menunggu', 'diterima'])
            ->pluck('siswa_id');

        return view('dashboard.admin.penempatan.create', [
            'siswa' => Siswa::whereHas('user', fn($q) => $q->where('is_active', 1))
                ->whereNotIn('id', $siswaTerpakai)
                ->orderBy('nama_lengkap')
                ->get(),

            'periode' => PeriodePkl::where('is_active', true)->get(),
            'tempat' => TempatPkl::orderBy('nama_perusahaan')->get()->map(function ($item) {
                $item->is_full = $item->sisaKuota() <= 0;
                return $item;
            }),
            'guru' => GuruPembimbing::whereHas('user', fn($q) => $q->where('is_active', 1))
                ->orderBy('nama_lengkap')->get(),
            'pembimbing' => PembimbingLapangan::whereHas('user', fn($q) => $q->where('is_active', 1))
                ->orderBy('nama_lengkap')->get(),
        ]);
    }

    // SIMPAN
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => [
                'required',
                Rule::unique('penempatan_pkl')
                    ->where(fn($q) => $q->where('status', 'aktif'))
            ],
            'periode_pkl_id' => 'required|exists:periode_pkl,id',
            'tempat_pkl_id' => 'required|exists:tempat_pkl,id',
            'guru_pembimbing_id' => 'required|exists:guru_pembimbing,id',
            'pembimbing_lapangan_id' => 'required|exists:pembimbing_lapangan,id',

            // TAMBAHAN MASA PKL
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        // CEK KUOTA TEMPAT PKL
        $tempat = TempatPkl::findOrFail($request->tempat_pkl_id);

        $totalSiswa = PenempatanPkl::withoutGlobalScope('aktif')
            ->where('tempat_pkl_id', $tempat->id)
            ->where('status', 'aktif')
            ->count();

        if ($totalSiswa >= $tempat->kuota_siswa) {
            return back()->withErrors([
                'tempat_pkl_id' => 'Kuota tempat PKL sudah penuh!'
            ])->withInput();
        }

        try {
            $tempatId = $request->tempat_pkl_id;
            DB::transaction(function () use ($request, $tempatId) {

                $tempat = TempatPkl::lockForUpdate()->findOrFail($tempatId);

                $totalSiswa = PenempatanPkl::withoutGlobalScope('aktif')
                    ->where('tempat_pkl_id', $tempat->id)
                    ->where('status', 'aktif')
                    ->lockForUpdate()
                    ->count();

                if ($totalSiswa >= $tempat->kuota_siswa) {
                    throw new \Exception('Kuota penuh');
                }

                PenempatanPkl::create([
                    'siswa_id' => $request->siswa_id,
                    'periode_pkl_id' => $request->periode_pkl_id,
                    'tempat_pkl_id' => $request->tempat_pkl_id,
                    'guru_pembimbing_id' => $request->guru_pembimbing_id,
                    'pembimbing_lapangan_id' => $request->pembimbing_lapangan_id,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'status' => 'nonaktif',
                    'status_pengajuan' => 'aktif',
                    'status_validasi' => 'menunggu',
                ]);

            });
        } catch (\Throwable $e) {
            return back()->withErrors([
                'tempat_pkl_id' => 'Kuota tempat PKL sudah penuh!'
            ])->withInput();
        }

        return redirect()->route('admin.penempatan.index')
            ->with('success', 'Penempatan PKL berhasil ditambahkan');
    }

    // FORM EDIT (LOGIKA AMAN)
    public function edit($id)
    {
        $penempatan = PenempatanPkl::withoutGlobalScope('aktif')->findOrFail($id);

        $siswaTerpakai = PenempatanPkl::withoutGlobalScope('aktif')
            ->where('status', 'aktif')
            ->where('id', '!=', $penempatan->id)
            ->pluck('siswa_id');

        return view('dashboard.admin.penempatan.edit', [
            'penempatan' => $penempatan,

            'siswa' => Siswa::whereHas('user', fn($q) => $q->where('is_active', 1))
                ->where(function ($q) use ($siswaTerpakai, $penempatan) {
                    $q->whereNotIn('id', $siswaTerpakai)
                        ->orWhere('id', $penempatan->siswa_id);
                })
                ->orderBy('nama_lengkap')
                ->get(),

            'tempat' => TempatPkl::orderBy('nama_perusahaan')->get()->map(function ($item) use ($penempatan) {

                $isDipakai = $item->id == $penempatan->tempat_pkl_id;

                // kalau tempat lama tetap bisa dipilih walaupun full
                $item->is_full = !$isDipakai && $item->sisaKuota() <= 0;

                return $item;
            }),
            'guru' => GuruPembimbing::orderBy('nama_lengkap')->get(),
            'pembimbing' => PembimbingLapangan::orderBy('nama_lengkap')->get(),
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $penempatan = PenempatanPkl::withoutGlobalScope('aktif')->findOrFail($id);

        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tempat_pkl_id' => 'required|exists:tempat_pkl,id',
            'guru_pembimbing_id' => 'required|exists:guru_pembimbing,id',
            'pembimbing_lapangan_id' => 'required|exists:pembimbing_lapangan,id',
            'status' => 'nullable|in:aktif,nonaktif',

            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        // CEK KUOTA (exclude dirinya sendiri)
        $tempat = TempatPkl::findOrFail($request->tempat_pkl_id);

        $totalSiswa = PenempatanPkl::withoutGlobalScope('aktif')
            ->where('tempat_pkl_id', $tempat->id)
            ->where('status', 'aktif')
            ->where('id', '!=', $penempatan->id)
            ->count();

        if ($totalSiswa >= $tempat->kuota_siswa) {
            return back()->withErrors([
                'tempat_pkl_id' => 'Kuota tempat PKL sudah penuh!'
            ])->withInput();
        }

        $data = [
            'siswa_id' => $request->siswa_id,
            'tempat_pkl_id' => $request->tempat_pkl_id,
            'guru_pembimbing_id' => $request->guru_pembimbing_id,
            'pembimbing_lapangan_id' => $request->pembimbing_lapangan_id,
        ];

        // tanggal opsional
        if ($request->filled('tanggal_mulai')) {
            $data['tanggal_mulai'] = $request->tanggal_mulai;
        }

        if ($request->filled('tanggal_selesai')) {
            $data['tanggal_selesai'] = $request->tanggal_selesai;
        }

        $statusBaru = $request->input('status');

        if ($statusBaru !== null && $statusBaru !== $penempatan->status) {
            $data['status_pengajuan'] = $statusBaru;

            // RESET VALIDASI HANYA JIKA ADA PERUBAHAN STATUS
            $data['status_validasi'] = 'menunggu';
            $data['catatan_validasi'] = null;
            $data['validated_by'] = null;
            $data['validated_at'] = null;
        }

        $penempatan->update($data);

        return redirect()
            ->route('admin.penempatan.index')
            ->with('success', 'Perubahan dikirim, menunggu validasi guru.');
    }

    // BULK UPDATE STATUS PER PERIODE
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_pkl,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $updated = PenempatanPkl::withoutGlobalScope('aktif')
            ->where('periode_pkl_id', $request->periode_id)
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->update([
                'status_pengajuan' => $request->status,
                'status_validasi' => 'menunggu',
                'validated_by' => null,
                'validated_at' => null,
            ]);

        return redirect()
            ->route('admin.penempatan.index', [
                'periode_id' => $request->periode_id
            ])
            ->with('success', "Berhasil mengajukan perubahan {$updated} data, menunggu validasi guru.");
    }

    // HAPUS
    public function destroy(PenempatanPkl $penempatan)
    {
        $penempatan->delete();
        return back()->with('success', 'Penempatan PKL berhasil dihapus');
    }
}
