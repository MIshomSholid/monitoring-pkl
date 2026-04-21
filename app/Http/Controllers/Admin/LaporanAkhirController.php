<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeriodePkl;
use App\Models\PenempatanPkl;
use Dompdf\Dompdf;
use Dompdf\Options;

class LaporanAkhirController extends Controller
{
    /**
     * ===============================
     * HALAMAN PILIH PERIODE
     * ===============================
     */
    public function index()
    {
        return view('dashboard.admin.laporan-akhir.index', [
            'periodeList' => PeriodePkl::orderBy('tanggal_mulai', 'desc')->get()
        ]);
    }

    /**
     * ===============================
     * PREVIEW DATA SISWA PER PERIODE
     * ===============================
     */
    public function preview(Request $request)
    {
        $request->validate([
            'periode_pkl_id' => 'required|exists:periode_pkl,id',
            'jenis_laporan'  => 'required|in:presensi,nilai,akhir'
        ]);

        $penempatan = PenempatanPkl::with([
            'siswa',
            'tempat',
            'guru',
            'pembimbingLapangan',
            'laporanAkhir' // 🔥 penting untuk cek status validasi guru
        ])
        ->where('periode_pkl_id', $request->periode_pkl_id)
        ->get();

        return view('dashboard.admin.laporan-akhir.preview', [
            'penempatan' => $penempatan,
            'periode'    => PeriodePkl::findOrFail($request->periode_pkl_id),
            'jenis'      => $request->jenis_laporan
        ]);
    }

    /**
     * ===============================
     * EXPORT PDF PER SISWA (FINAL + VALIDASI GURU)
     * ===============================
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'penempatan_id' => 'required|exists:penempatan_pkl,id',
            'jenis_laporan' => 'required|in:presensi,nilai,akhir'
        ]);

        $penempatan = PenempatanPkl::with([
            'siswa',
            'tempat',
            'guru',
            'pembimbingLapangan',
            'laporanAkhir',
            'presensi',
            'laporanKegiatan',
            'penilaianKpi.indikator.kategori'
        ])->findOrFail($request->penempatan_id);

        /**
         * 🔒 PROTEKSI:
         * Hanya boleh cetak jika sudah disetujui Guru Pembimbing
         */
        if (
            !$penempatan->laporanAkhir ||
            $penempatan->laporanAkhir->status_validasi !== 'diterima'
        ) {
            return back()->with('error', 'Laporan belum disetujui Guru Pembimbing.');
        }

        /**
         * ===============================
         * KONFIGURASI DOMPDF
         * ===============================
         */
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view(
            'dashboard.admin.laporan-akhir.pdf.' . $request->jenis_laporan,
            [
                'penempatan' => $penempatan
            ]
        )->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream(
            'Laporan-PKL-' . str_replace(' ', '_', $penempatan->siswa->nama_lengkap) . '.pdf',
            ['Attachment' => true]
        );
    }
}
