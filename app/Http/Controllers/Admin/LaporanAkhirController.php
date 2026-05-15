<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeriodePkl;
use App\Models\PenempatanPkl;
use Dompdf\Dompdf;
use Dompdf\Options;
use ZipArchive;
use Illuminate\Support\Facades\File;

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
            'jenis_laporan' => 'required|in:presensi,nilai,akhir'
        ]);

        $penempatan = PenempatanPkl::with([
            'siswa',
            'tempat',
            'guru',
            'pembimbingLapangan',
            'laporanAkhir'
        ])
            ->where('periode_pkl_id', $request->periode_pkl_id)
            ->get();

        return view('dashboard.admin.laporan-akhir.preview', [
            'penempatan' => $penempatan,
            'periode' => PeriodePkl::findOrFail($request->periode_pkl_id),
            'jenis' => $request->jenis_laporan
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

    /**
     * ===============================
     * EXPORT SEMUA PDF PER PERIODE
     * ===============================
     */
    public function exportAllPdf(Request $request)
    {
        $request->validate([
            'periode_pkl_id' => 'required|exists:periode_pkl,id',
            'jenis_laporan' => 'required|in:presensi,nilai,akhir'
        ]);

        /**
         * Ambil semua penempatan
         * yang SUDAH disetujui guru
         */
        $penempatanList = PenempatanPkl::with([
            'siswa',
            'tempat',
            'guru',
            'pembimbingLapangan',
            'laporanAkhir',
            'presensi',
            'laporanKegiatan',
            'penilaianKpi.indikator.kategori'
        ])
            ->where('periode_pkl_id', $request->periode_pkl_id)
            ->whereHas('laporanAkhir', function ($q) {
                $q->where('status_validasi', 'diterima');
            })
            ->get();

        if ($penempatanList->isEmpty()) {
            return back()->with('error', 'Tidak ada laporan yang disetujui.');
        }

        /**
         * Folder temporary
         */
        $tempPath = storage_path('app/temp-pdf-' . time());

        if (!File::exists($tempPath)) {
            File::makeDirectory($tempPath, 0755, true);
        }

        /**
         * Nama ZIP
         */
        $jenisLabel = match ($request->jenis_laporan) {
            'presensi' => 'Rekap-Presensi',
            'nilai' => 'Rekap-Nilai',
            'akhir' => 'Rekap-Laporan-Akhir',
        };

        $periode = PeriodePkl::findOrFail($request->periode_pkl_id);

        $namaPeriode = str_replace(
            [' ', '/'],
            ['-', '-'],
            $periode->nama_periode
        );

        $zipFileName = 'Laporan-PKL-' .
            $jenisLabel . '-' .
            $namaPeriode .
            '.zip';

        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {

            $jenisPdf = match ($request->jenis_laporan) {
                'presensi' => 'Presensi',
                'nilai' => 'Nilai',
                'akhir' => 'Laporan-Akhir',
            };

            foreach ($penempatanList as $penempatan) {

                /**
                 * DOMPDF
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

                /**
                 * Nama file PDF
                 */
                $nis = str_replace('/', '_', $penempatan->siswa->nis);

                $pdfName = 'Laporan-' .
                    $jenisPdf . '-' .
                    $nis . '-' .
                    str_replace(' ', '_', $penempatan->siswa->nama_lengkap)
                    . '.pdf';

                $pdfPath = $tempPath . '/' . $pdfName;

                file_put_contents($pdfPath, $dompdf->output());

                /**
                 * Masukkan ke ZIP
                 */
                $zip->addFile($pdfPath, $pdfName);
            }

            $zip->close();
        }

        File::deleteDirectory($tempPath);

        /**
         * Hapus temporary setelah download
         */
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
