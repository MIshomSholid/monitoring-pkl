<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanKegiatan;
use App\Models\Presensi;
use Cloudinary\Cloudinary;

class LaporanKegiatanController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $penempatan = $siswa?->penempatanAktif;

        if (!$penempatan) {
            abort(403, 'Anda belum memiliki penempatan PKL aktif');
        }

        $today = now()->toDateString();

        // 🔒 CEK PRESENSI
        $presensiHariIni = Presensi::where('penempatan_pkl_id', $penempatan->id)
            ->where('tanggal', $today)
            ->first();

        $bolehInput = false;
        $pesan = null;

        if (!$presensiHariIni) {
            $pesan = 'Anda harus melakukan presensi terlebih dahulu hari ini.';
        } elseif ($presensiHariIni->jenis_presensi !== 'hadir') {
            $pesan = 'Laporan hanya dapat diisi jika presensi hari ini berstatus HADIR.';
        } else {
            $bolehInput = true;
        }

        $sudahLapor = LaporanKegiatan::where('penempatan_pkl_id', $penempatan->id)
            ->where('tanggal', $today)
            ->exists();

        $laporan = LaporanKegiatan::where('penempatan_pkl_id', $penempatan->id)
            ->latest('tanggal')
            ->get();

        return view(
            'dashboard.siswa-dashboard.laporan-kegiatan.index',
            compact('laporan', 'sudahLapor', 'bolehInput', 'pesan')
        );
    }

    public function store(Request $request)
    {
        $siswa = Auth::user()->siswa;
        $penempatan = $siswa?->penempatanAktif;

        if (!$penempatan) {
            abort(403, 'Anda belum memiliki penempatan PKL aktif');
        }

        $today = now()->toDateString();

        // 🔒 VALIDASI PRESENSI
        $presensiHariIni = Presensi::where('penempatan_pkl_id', $penempatan->id)
            ->where('tanggal', $today)
            ->first();

        if (!$presensiHariIni || $presensiHariIni->jenis_presensi !== 'hadir') {
            abort(403, 'Anda tidak diizinkan mengisi laporan hari ini.');
        }

        // 🔒 VALIDASI 1 HARI 1 LAPORAN
        $exists = LaporanKegiatan::where('penempatan_pkl_id', $penempatan->id)
            ->where('tanggal', $today)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'laporan' => 'Laporan kegiatan hari ini sudah dikirim.'
            ]);
        }

        $request->validate([
            'deskripsi_kegiatan' => 'required|string',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $filePath = null;

        if ($request->hasFile('file_bukti')) {

            $upload = $this->cloudinary()->uploadApi()->upload(
                $request->file('file_bukti')->getRealPath(),
                [
                    'folder' => 'laporan-kegiatan',
                    'resource_type' => 'auto' 
                ]
            );

            $filePath = $upload['secure_url'];
        }

        LaporanKegiatan::create([
            'penempatan_pkl_id' => $penempatan->id,
            'tanggal' => $today,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'file_bukti' => $filePath,
            'status_validasi' => 'menunggu',
        ]);

        return redirect()
            ->route('siswa.laporan-kegiatan.index')
            ->with('success', 'Laporan kegiatan berhasil dikirim.');
    }

    private function cloudinary()
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
    }
}