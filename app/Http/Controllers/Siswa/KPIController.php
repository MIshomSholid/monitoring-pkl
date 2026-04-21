<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PenempatanPkl;
use App\Models\PenilaianKpi;
use App\Models\Presensi;
use App\Models\LaporanKegiatan;
use App\Models\KpiKategori;
use Carbon\Carbon;

class KPIController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $penempatan = $this->getPenempatanAktif($userId);

        // ambil semua KPI untuk perhitungan
        $kpi = $this->getKpi($penempatan->id);

        /*
        =================================
        TANGGAL AKTIF
        =================================
        */

        $tanggalAktif = request('tanggal');

        if (!$tanggalAktif) {
            $tanggalAktif = PenilaianKpi::where('penempatan_pkl_id', $penempatan->id)
                ->where('status_validasi', 'diterima')
                ->max('periode_penilaian');
        }

        /*
        =================================
        DATA KPI SESUAI TANGGAL
        =================================
        */

        $kpiTerbaru = PenilaianKpi::with('indikator.kategori')
            ->where('penempatan_pkl_id', $penempatan->id)
            ->whereDate('periode_penilaian', $tanggalAktif)
            ->where('status_validasi', 'diterima')
            ->get();

        /*
        =================================
        NAVIGASI HARI
        =================================
        */

        $prev = null;
        $next = null;

        if ($tanggalAktif) {

            $prev = PenilaianKpi::where('penempatan_pkl_id', $penempatan->id)
                ->where('status_validasi', 'diterima')
                ->whereDate('periode_penilaian', '<', $tanggalAktif)
                ->max('periode_penilaian');

            $next = PenilaianKpi::where('penempatan_pkl_id', $penempatan->id)
                ->where('status_validasi', 'diterima')
                ->whereDate('periode_penilaian', '>', $tanggalAktif)
                ->min('periode_penilaian');

        }

        /* ===============================
            NILAI MENTAH TIAP ASPEK
        =============================== */

        $nilaiTeknis = $this->hitungTeknis($kpi);
        $nilaiNonTeknis = $this->hitungNonTeknis($kpi);
        $nilaiPresensi = $this->hitungPresensi($penempatan);
        $nilaiLaporan = $this->hitungLaporan($penempatan->id);

        /* ===============================
            AMBIL BOBOT KATEGORI
        =============================== */

        $bobot = KpiKategori::pluck('bobot_persen', 'nama_kategori');

        $bobotTeknis = ($bobot['Aspek Teknis'] ?? 0) / 100;
        $bobotNonTeknis = ($bobot['Aspek Non-Teknis'] ?? 0) / 100;
        $bobotPresensi = ($bobot['Aspek Presensi'] ?? 0) / 100;
        $bobotLaporan = ($bobot['Aspek Laporan'] ?? 0) / 100;

        /* ===============================
            NILAI AKHIR BERBOBOT
        =============================== */

        $nilaiAkhir =
            ($nilaiTeknis * $bobotTeknis) +
            ($nilaiNonTeknis * $bobotNonTeknis) +
            ($nilaiPresensi * $bobotPresensi) +
            ($nilaiLaporan * $bobotLaporan);

        $nilaiAkhir = round($nilaiAkhir, 2);

        $groupedKpi = $kpiTerbaru->groupBy('periode_penilaian');

        return view('dashboard.siswa-dashboard.kpi.index', compact(
            'penempatan',
            'nilaiTeknis',
            'nilaiNonTeknis',
            'nilaiPresensi',
            'nilaiLaporan',
            'nilaiAkhir',
            'kpiTerbaru',
            'tanggalAktif',
            'prev',
            'next'
        ));
    }

    /* =====================================================
        PENEMPATAN AKTIF
    =====================================================*/

    private function getPenempatanAktif($userId)
    {
        return PenempatanPkl::with(['tempatPkl'])
            ->whereHas('siswa', fn($q) => $q->where('user_id', $userId))
            ->firstOrFail();
    }

    /* =====================================================
        SEMUA DATA KPI
    =====================================================*/

    private function getKpi($penempatanId)
    {
        return PenilaianKpi::with('indikator.kategori')
            ->where('penempatan_pkl_id', $penempatanId)
            ->where('status_validasi', 'diterima')
            ->get();
    }

    /* =====================================================
        KPI TERBARU
    =====================================================*/

    private function getKpiTerbaru($penempatanId)
    {
        $tanggal = PenilaianKpi::where('penempatan_pkl_id', $penempatanId)
            ->where('status_validasi', 'diterima')
            ->max('periode_penilaian');

        return PenilaianKpi::with('indikator.kategori')
            ->where('penempatan_pkl_id', $penempatanId)
            ->whereDate('periode_penilaian', $tanggal)
            ->where('status_validasi', 'diterima')
            ->get();
    }

    /* =====================================================
        TEKNIS
    =====================================================*/

    private function hitungTeknis($kpi)
    {
        $data = $kpi->filter(
            fn($i) => optional($i->indikator->kategori)->nama_kategori === 'Aspek Teknis'
        );

        return $data->count()
            ? round($data->avg('nilai'), 2)
            : 0;
    }

    /* =====================================================
        NON TEKNIS
    =====================================================*/

    private function hitungNonTeknis($kpi)
    {
        $data = $kpi->filter(
            fn($i) => optional($i->indikator->kategori)->nama_kategori === 'Aspek Non-Teknis'
        );

        $rataPerTanggal = $data
            ->groupBy('periode_penilaian')
            ->map(fn($items) => $items->avg('nilai'));

        return $rataPerTanggal->count()
            ? round($rataPerTanggal->avg(), 2)
            : 0;
    }

    /* =====================================================
        PRESENSI
    =====================================================*/

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
            'sunday' => 'minggu'
        ];

        $jamMasuk = $penempatan->tempatPkl->jam_masuk;
        $toleransi = $penempatan->tempatPkl->toleransi_keterlambatan ?? 0;

        $cursor = $tanggalMulai->copy();
        $tanggalWajib = collect();

        while ($cursor <= $batasAkhir) {

            $hari = $mappingHari[strtolower($cursor->format('l'))] ?? '';

            if ($hariWajib->contains($hari)) {
                $tanggalWajib->push($cursor->toDateString());
            }

            $cursor->addDay();
        }

        $presensi = Presensi::where('penempatan_pkl_id', $penempatan->id)
            ->whereBetween('tanggal', [$tanggalMulai, $batasAkhir])
            ->get()
            ->keyBy(fn($i) => Carbon::parse($i->tanggal)->toDateString());

        $totalNilai = 0;

        foreach ($tanggalWajib as $tgl) {

            $p = $presensi->get($tgl);

            if (!$p)
                continue;

            if ($p->jenis_presensi === 'hadir' && $p->status_validasi === 'diterima') {

                $jamPresensi = Carbon::parse($p->waktu_presensi);
                $jamBatas = Carbon::parse($jamMasuk)->addMinutes($toleransi);

                $totalNilai += $jamPresensi->gt($jamBatas) ? 80 : 100;

            } elseif ($p->jenis_presensi === 'izin' && $p->status_validasi === 'diterima') {

                $totalNilai += 85;

            } elseif ($p->jenis_presensi === 'libur' && $p->status_validasi === 'diterima') {

                $totalNilai += 100;
            }
        }

        return $tanggalWajib->count()
            ? round($totalNilai / $tanggalWajib->count(), 2)
            : 0;
    }

    /* =====================================================
        LAPORAN
    =====================================================*/

    private function hitungLaporan($penempatanId)
    {
        $total = LaporanKegiatan::where('penempatan_pkl_id', $penempatanId)->count();

        $valid = LaporanKegiatan::where('penempatan_pkl_id', $penempatanId)
            ->where('status_validasi', 'diterima')
            ->count();

        return $total
            ? round(($valid / $total) * 100, 2)
            : 0;
    }
}