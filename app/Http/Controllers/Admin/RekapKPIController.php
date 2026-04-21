<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PenempatanPkl;
use App\Models\PenilaianKpi;
use App\Models\PeriodePkl;
use App\Models\TempatPkl;
use App\Models\Presensi;
use App\Models\LaporanKegiatan;
use App\Models\KpiKategori;

class RekapKPIController extends Controller
{
    public function index(Request $request)
    {
        $bulan = Carbon::now();

        $rekap = $this->getFilteredPenempatan($request);

        $bobot = KpiKategori::pluck('bobot_persen', 'nama_kategori');

        $bobotTeknis = ($bobot['Aspek Teknis'] ?? 0) / 100;
        $bobotNonTeknis = ($bobot['Aspek Non-Teknis'] ?? 0) / 100;
        $bobotPresensi = ($bobot['Aspek Presensi'] ?? 0) / 100;
        $bobotLaporan = ($bobot['Aspek Laporan'] ?? 0) / 100;

        foreach ($rekap as $penempatan) {

            $nilaiTeknis = $this->hitungTeknis($penempatan, $bulan);
            $nilaiNonTeknis = $this->hitungNonTeknis($penempatan, $bulan);
            $nilaiPresensi = $this->hitungPresensi($penempatan);
            $nilaiLaporan = $this->hitungLaporan($penempatan);

            // nilai mentah
            $penempatan->nilaiTeknis = $nilaiTeknis;
            $penempatan->nilaiNonTeknis = $nilaiNonTeknis;
            $penempatan->nilaiPresensi = $nilaiPresensi;
            $penempatan->nilaiLaporan = $nilaiLaporan;

            // nilai akhir berbobot
            $penempatan->rataTotal = round(
                ($nilaiTeknis * $bobotTeknis) +
                ($nilaiNonTeknis * $bobotNonTeknis) +
                ($nilaiPresensi * $bobotPresensi) +
                ($nilaiLaporan * $bobotLaporan),
                2
            );
        }

        return view('dashboard.admin.rekap-kpi.index', [
            'rekap' => $rekap,
            'periodeList' => PeriodePkl::orderBy('nama_periode')->get(),
            'tempatList' => TempatPkl::orderBy('nama_perusahaan')->get(),
        ]);
    }

    /*
    ======================================================
    FILTER PENEMPATAN
    ======================================================
    */
    private function getFilteredPenempatan(Request $request)
    {
        $query = PenempatanPkl::with(['siswa', 'tempatPkl', 'periodePkl'])
            ->where('status_validasi', 'diterima');

        if ($request->filled('nama_siswa')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama_siswa . '%');
            });
        }

        if ($request->filled('tempat_pkl_id')) {
            $query->where('tempat_pkl_id', $request->tempat_pkl_id);
        }

        if ($request->filled('periode_pkl_id')) {
            $query->where('periode_pkl_id', $request->periode_pkl_id);
        }

        return $query->get();
    }

    /*
    ======================================================
    HITUNG TEKNIS
    ======================================================
    */
    private function hitungTeknis($penempatan, $bulan)
    {
        $kpi = $this->getKpiBulanan($penempatan, $bulan);

        $data = $kpi->filter(
            fn($i) => optional($i->indikator->kategori)->nama_kategori === 'Aspek Teknis'
        );

        return $data->count() > 0
            ? round($data->avg('nilai'), 2)
            : 0;
    }

    /*
    ======================================================
    HITUNG NON TEKNIS
    ======================================================
    */
    private function hitungNonTeknis($penempatan, $bulan)
    {
        $kpi = $this->getKpiBulanan($penempatan, $bulan);

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

    /*
    ======================================================
    GET KPI BULANAN
    ======================================================
    */
    private function getKpiBulanan($penempatan, $bulan)
    {
        return PenilaianKpi::with('indikator.kategori')
            ->where('penempatan_pkl_id', $penempatan->id)
            ->where('status_validasi', 'diterima')
            ->get();
    }

    /*
    ======================================================
    HITUNG PRESENSI (SAMA DENGAN SISWA)
    ======================================================
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

            if ($p->jenis_presensi === 'hadir' && $p->status_validasi === 'diterima') {

                $jamPresensi = Carbon::parse($p->waktu_presensi);
                $jamBatas = Carbon::parse($jamMasuk)->addMinutes($toleransiMenit);

                $totalNilai += $jamPresensi->gt($jamBatas) ? 80 : 100;

            } elseif ($p->jenis_presensi === 'izin' && $p->status_validasi === 'diterima') {
                $totalNilai += 85;

            } elseif ($p->jenis_presensi === 'libur' && $p->status_validasi === 'diterima') {
                $totalNilai += 100;
            }
        }

        return $tanggalWajib->count() > 0
            ? round($totalNilai / $tanggalWajib->count(), 2)
            : 0;
    }

    /*
    ======================================================
    HITUNG LAPORAN
    ======================================================
    */
    private function hitungLaporan($penempatan)
    {
        $total = LaporanKegiatan::where('penempatan_pkl_id', $penempatan->id)
            ->count();

        $valid = LaporanKegiatan::where('penempatan_pkl_id', $penempatan->id)
            ->where('status_validasi', 'diterima')
            ->count();

        return $total > 0
            ? round(($valid / $total) * 100, 2)
            : 0;
    }
}
