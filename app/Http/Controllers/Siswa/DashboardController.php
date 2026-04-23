<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\PenempatanPkl;
use App\Models\Presensi;
use App\Models\LaporanKegiatan;
use App\Models\PenilaianKpi;
use App\Models\CatatanEvaluasi;
use App\Models\KpiKategori;

class DashboardController extends Controller
{
    public function index()
    {
        $siswa = $this->getSiswa();
        $penempatan = $this->getPenempatanAktif($siswa->id);

        $statusPresensi = $this->getStatusPresensiHariIni($penempatan->id);
        $laporanTerakhir = $this->getLaporanTerakhir($penempatan->id);

        $kpi = $this->hitungKpi($penempatan);

        $catatanTerbaru = $this->getCatatanTerbaru($penempatan->id);

        return view('dashboard.siswa-dashboard.index', [
            'siswa' => $siswa,
            'penempatan' => $penempatan,
            'statusPresensi' => $statusPresensi,
            'laporanTerakhir' => $laporanTerakhir,
            'nilaiTeknis' => $kpi['teknis'],
            'nilaiNonTeknis' => $kpi['non_teknis'],
            'nilaiPresensi' => $kpi['presensi'],
            'nilaiLaporan' => $kpi['laporan'],
            'totalKpi' => $kpi['total'],
            'catatanTerbaru' => $catatanTerbaru,
        ]);
    }

    private function getSiswa()
    {
        return Siswa::where('user_id', Auth::id())->firstOrFail();
    }

    private function getPenempatanAktif($siswaId)
    {
        $penempatan = PenempatanPkl::with([
            'tempatPkl',
            'periodePkl',
            'guruPembimbing',
            'pembimbingLapangan'
        ])
            ->where('siswa_id', $siswaId)
            ->where('status', 'aktif') 
            ->where('status_validasi', 'diterima')
            ->first();

        if (!$penempatan) {
            abort(403, 'Anda tidak memiliki PKL aktif.');
        }

        return $penempatan;
    }

    private function getStatusPresensiHariIni($penempatanId)
    {
        $today = Carbon::today();

        $presensi = Presensi::where('penempatan_pkl_id', $penempatanId)
            ->whereDate('tanggal', $today)
            ->first();

        return $presensi
            ? ucfirst($presensi->jenis_presensi)
            : 'Belum Presensi';
    }

    private function getLaporanTerakhir($penempatanId)
    {
        return LaporanKegiatan::where('penempatan_pkl_id', $penempatanId)
            ->latest()
            ->first();
    }

    private function getCatatanTerbaru($penempatanId)
    {
        return CatatanEvaluasi::where('penempatan_pkl_id', $penempatanId)
            ->latest()
            ->first();
    }

    private function hitungKpi($penempatan)
    {
        $kpi = PenilaianKpi::with('indikator.kategori')
            ->where('penempatan_pkl_id', $penempatan->id)
            ->where('status_validasi', 'diterima')
            ->get();

        $nilaiTeknis = $this->hitungTeknis($kpi);
        $nilaiNonTeknis = $this->hitungNonTeknis($kpi);
        $nilaiPresensi = $this->hitungPresensi($penempatan);
        $nilaiLaporan = $this->hitungLaporan($penempatan->id);

        /*
        =========================
        AMBIL BOBOT KATEGORI
        =========================
        */

        $bobot = KpiKategori::pluck('bobot_persen', 'nama_kategori');

        $bobotTeknis = ($bobot['Aspek Teknis'] ?? 0) / 100;
        $bobotNonTeknis = ($bobot['Aspek Non-Teknis'] ?? 0) / 100;
        $bobotPresensi = ($bobot['Aspek Presensi'] ?? 0) / 100;
        $bobotLaporan = ($bobot['Aspek Laporan'] ?? 0) / 100;

        /*
        =========================
        NILAI AKHIR
        =========================
        */

        $total =
            ($nilaiTeknis * $bobotTeknis) +
            ($nilaiNonTeknis * $bobotNonTeknis) +
            ($nilaiPresensi * $bobotPresensi) +
            ($nilaiLaporan * $bobotLaporan);

        return [
            'teknis' => round($nilaiTeknis, 2),
            'non_teknis' => round($nilaiNonTeknis, 2),
            'presensi' => round($nilaiPresensi, 2),
            'laporan' => round($nilaiLaporan, 2),
            'total' => round($total, 2),
        ];
    }

    private function hitungTeknis($kpi)
    {
        $data = $kpi->filter(
            fn($i) => optional($i->indikator->kategori)->nama_kategori === 'Aspek Teknis'
        );

        return $data->count() > 0
            ? round($data->avg('nilai'), 2)
            : 0;
    }

    private function hitungNonTeknis($kpi)
    {
        $data = $kpi->filter(
            fn($i) => optional($i->indikator->kategori)->nama_kategori === 'Aspek Non-Teknis'
        );

        $rataPerTanggal = $data
            ->groupBy('periode_penilaian')
            ->map(fn($items) => $items->avg('nilai'));

        return $rataPerTanggal->count() > 0
            ? round($rataPerTanggal->avg(), 2)
            : 0;
    }

    private function hitungPresensi($penempatan)
    {
        $tanggalMulai = Carbon::parse($penempatan->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($penempatan->tanggal_selesai);

        $batasAkhir = now()->lessThan($tanggalSelesai)
            ? now()
            : $tanggalSelesai;

        $hariWajib = collect($penempatan->tempatPkl->hari_wajib ?? [])
            ->map(fn($h) => strtolower(trim($h)));

        $mappingHari = [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
            'sunday' => 'minggu',
        ];

        $jamMasuk = $penempatan->tempatPkl->jam_masuk;
        $toleransiMenit = $penempatan->tempatPkl->toleransi_keterlambatan ?? 0;

        $cursor = $tanggalMulai->copy();
        $tanggalWajib = collect();

        while ($cursor <= $batasAkhir) {

            $englishDay = strtolower($cursor->format('l'));
            $namaHari = $mappingHari[$englishDay] ?? '';

            if ($hariWajib->contains($namaHari)) {
                $tanggalWajib->push($cursor->toDateString());
            }

            $cursor->addDay();
        }

        $presensi = Presensi::where('penempatan_pkl_id', $penempatan->id)
            ->whereBetween('tanggal', [$tanggalMulai, $batasAkhir])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->tanggal)->toDateString());

        $totalNilai = 0;

        foreach ($tanggalWajib as $tanggal) {

            $p = $presensi->get($tanggal);

            if (!$p) {
                continue;
            }

            if ($p->jenis_presensi === 'hadir') {

                if ($p->status_validasi !== 'diterima') {
                    continue;
                }

                $jamPresensi = Carbon::parse($p->waktu_presensi);
                $jamBatas = Carbon::parse($jamMasuk)
                    ->addMinutes($toleransiMenit);

                if ($jamPresensi->gt($jamBatas)) {
                    $totalNilai += 80;
                } else {
                    $totalNilai += 100;
                }

            } elseif ($p->jenis_presensi === 'izin') {

                $totalNilai += $p->status_validasi === 'diterima' ? 85 : 0;

            } elseif ($p->jenis_presensi === 'libur') {

                $totalNilai += $p->status_validasi === 'diterima' ? 100 : 0;

            } elseif ($p->jenis_presensi === 'terlambat') {

                $totalNilai += 80;
            }
        }

        return $tanggalWajib->count() > 0
            ? round($totalNilai / $tanggalWajib->count(), 2)
            : 0;
    }

    private function hitungLaporan($penempatanId)
    {
        $total = LaporanKegiatan::where('penempatan_pkl_id', $penempatanId)
            ->count();

        $valid = LaporanKegiatan::where('penempatan_pkl_id', $penempatanId)
            ->where('status_validasi', 'diterima')
            ->count();

        return $total > 0
            ? round(($valid / $total) * 100, 2)
            : 0;
    }
}