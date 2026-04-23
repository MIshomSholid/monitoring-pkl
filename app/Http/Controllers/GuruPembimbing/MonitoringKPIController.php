<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GuruPembimbing;
use App\Models\PenempatanPkl;
use App\Models\PenilaianKpi;
use App\Models\Presensi;
use App\Models\LaporanKegiatan;
use App\Models\KpiKategori;
use Carbon\Carbon;

class MonitoringKPIController extends Controller
{
    public function index(Request $request)
    {
        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        $penempatan = PenempatanPkl::with('siswa')
            ->where('guru_pembimbing_id', $guru->id)
            ->where('status', 'aktif')
            ->where('status_validasi', 'diterima')
            ->get();

        $penempatanId = $request->penempatan_pkl_id;

        $kpi = collect();
        $grafik = collect();
        $rataRata = null;

        $tanggalAktif = null;
        $prev = null;
        $next = null;

        $nilaiTeknisBobot = 0;
        $nilaiNonTeknisBobot = 0;
        $nilaiPresensiBobot = 0;
        $nilaiLaporanBobot = 0;

        $bobotTeknis = 0;
        $bobotNonTeknis = 0;
        $bobotPresensi = 0;
        $bobotLaporan = 0;

        $penempatanModel = null;

        if ($penempatanId) {

            $penempatanModel = PenempatanPkl::with('tempatPkl', 'siswa')
                ->where('id', $penempatanId)
                ->where('guru_pembimbing_id', $guru->id)
                ->first();

            if (!$penempatanModel) {
                return redirect()->back()->withErrors('Data tidak ditemukan atau sudah tidak aktif');
            }

            $semuaKpi = PenilaianKpi::with('indikator.kategori')
                ->where('penempatan_pkl_id', $penempatanId)
                ->where('status_validasi', 'diterima')
                ->get();

            // ambil KPI terbaru untuk tabel
            $tanggalList = PenilaianKpi::where('penempatan_pkl_id', $penempatanId)
                ->where('status_validasi', 'diterima')
                ->selectRaw('DATE(periode_penilaian) as tanggal')
                ->distinct()
                ->orderByDesc('tanggal')
                ->pluck('tanggal');

            $tanggalAktif = $request->tanggal ?? ($tanggalList->first() ?? null);

            $index = $tanggalList->search($tanggalAktif);

            if ($index !== false) {
                $prev = $tanggalList[$index + 1] ?? null;
                $next = $tanggalList[$index - 1] ?? null;
            } else {
                $prev = null;
                $next = null;
            }

            if ($tanggalAktif) {
                $kpi = PenilaianKpi::with('indikator.kategori')
                    ->where('penempatan_pkl_id', $penempatanId)
                    ->whereDate('periode_penilaian', $tanggalAktif)
                    ->where('status_validasi', 'diterima')
                    ->get();
            } else {
                $kpi = collect();
            }

            $nilaiTeknis = $this->hitungTeknis($semuaKpi);
            $nilaiNonTeknis = $this->hitungNonTeknis($semuaKpi);
            $nilaiPresensi = $this->hitungPresensi($penempatanModel);
            $nilaiLaporan = $this->hitungLaporan($penempatanId);

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
            NILAI AKHIR BERBOBOT
            =========================
            */

            $rataRata = round(
                ($nilaiTeknis * $bobotTeknis) +
                ($nilaiNonTeknis * $bobotNonTeknis) +
                ($nilaiPresensi * $bobotPresensi) +
                ($nilaiLaporan * $bobotLaporan),
                2
            );

            /*
            =========================
            NILAI BERBOBOT PER ASPEK
            =========================
            */

            $nilaiTeknisBobot = round($nilaiTeknis * $bobotTeknis, 2);
            $nilaiNonTeknisBobot = round($nilaiNonTeknis * $bobotNonTeknis, 2);
            $nilaiPresensiBobot = round($nilaiPresensi * $bobotPresensi, 2);
            $nilaiLaporanBobot = round($nilaiLaporan * $bobotLaporan, 2);

            /*
            =========================
            DATA GRAFIK (MENTAH)
            =========================
            */

            $grafik = collect([
                'Teknis' => $nilaiTeknis,
                'Non Teknis' => $nilaiNonTeknis,
                'Presensi' => $nilaiPresensi,
                'Laporan' => $nilaiLaporan,
            ]);
        }

        return view(
            'dashboard.guru-dashboard.monitoring-kpi.index',
            compact(
                'penempatan',
                'penempatanModel',
                'kpi',
                'grafik',
                'rataRata',
                'penempatanId',
                'tanggalAktif',
                'prev',
                'next',

                'nilaiTeknisBobot',
                'nilaiNonTeknisBobot',
                'nilaiPresensiBobot',
                'nilaiLaporanBobot',

                'bobotTeknis',
                'bobotNonTeknis',
                'bobotPresensi',
                'bobotLaporan'
            )
        );
    }

    /*
    =====================================================
    AMBIL SEMUA KPI
    =====================================================
    */

    // private function getKpi($penempatanId)
    // {
    //     return PenilaianKpi::with('indikator.kategori')
    //         ->where('penempatan_pkl_id', $penempatanId)
    //         ->where('status_validasi', 'diterima')
    //         ->get();
    // }

    /*
    =====================================================
    KPI TERBARU
    =====================================================
    */

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

    /*
    =====================================================
    HITUNG TEKNIS
    =====================================================
    */

    private function hitungTeknis($kpi)
    {
        $data = $kpi->filter(
            fn($i) => optional($i->indikator->kategori)->nama_kategori === 'Aspek Teknis'
        );

        return $data->count()
            ? round($data->avg('nilai'), 2)
            : 0;
    }

    /*
    =====================================================
    HITUNG NON TEKNIS
    =====================================================
    */

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

    /*
    =====================================================
    HITUNG PRESENSI
    =====================================================
    */

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
            ->keyBy(fn($p) => Carbon::parse($p->tanggal)->toDateString());

        $jamMasuk = $penempatan->tempatPkl->jam_masuk;
        $toleransi = $penempatan->tempatPkl->toleransi_keterlambatan ?? 0;

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

            } elseif ($p->jenis_presensi === 'terlambat') {

                $totalNilai += 80;
            }
        }

        return $tanggalWajib->count()
            ? round($totalNilai / $tanggalWajib->count(), 2)
            : 0;
    }

    /*
    =====================================================
    HITUNG LAPORAN
    =====================================================
    */

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