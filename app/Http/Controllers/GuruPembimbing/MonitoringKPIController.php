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
    // Menampilkan halaman monitoring KPI siswa bimbingan
    public function index(Request $request)
    {
        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        $penempatan = $this->getPenempatanAktif($guru);

        $penempatanId = $request->penempatan_pkl_id;

        $defaults = $this->defaultState();

        if (!$penempatanId) {
            return view('dashboard.guru-dashboard.monitoring-kpi.index',
                array_merge($defaults, compact('penempatan', 'penempatanId'))
            );
        }

        $penempatanModel = $this->findPenempatan($penempatanId, $guru);

        if (!$penempatanModel) {
            return redirect()->back()->withErrors('Data tidak ditemukan atau sudah tidak aktif');
        }

        $semuaKpi = $this->getAllKpi($penempatanId);

        [$tanggalAktif, $prev, $next] = $this->resolveTanggalNavigasi($penempatanId, $request->tanggal);

        $kpi = $tanggalAktif
            ? $this->getKpiByTanggal($penempatanId, $tanggalAktif)
            : collect();

        $bobot = $this->getBobot();

        [$nilaiTeknisBobot, $nilaiNonTeknisBobot, $nilaiPresensiBobot, $nilaiLaporanBobot, $rataRata] =
            $this->hitungNilaiAkhir($semuaKpi, $penempatanModel, $penempatanId, $bobot);

        $grafik = collect([
            'Teknis'     => $this->hitungTeknis($semuaKpi),
            'Non Teknis' => $this->hitungNonTeknis($semuaKpi),
            'Presensi'   => $this->hitungPresensi($penempatanModel),
            'Laporan'    => $this->hitungLaporan($penempatanId),
        ]);

        return view('dashboard.guru-dashboard.monitoring-kpi.index', compact(
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
        ) + [
            'bobotTeknis'    => $bobot['teknis'],
            'bobotNonTeknis' => $bobot['nonTeknis'],
            'bobotPresensi'  => $bobot['presensi'],
            'bobotLaporan'   => $bobot['laporan'],
        ]);
    }

    // Menyediakan nilai awal ketika belum terdapat data yang dipilih
    private function defaultState(): array
    {
        return [
            'penempatanModel'    => null,
            'kpi'                => collect(),
            'grafik'             => collect(),
            'rataRata'           => null,
            'tanggalAktif'       => null,
            'prev'               => null,
            'next'               => null,
            'nilaiTeknisBobot'   => 0,
            'nilaiNonTeknisBobot' => 0,
            'nilaiPresensiBobot' => 0,
            'nilaiLaporanBobot'  => 0,
            'bobotTeknis'        => 0,
            'bobotNonTeknis'     => 0,
            'bobotPresensi'      => 0,
            'bobotLaporan'       => 0,
        ];
    }

    // Mengambil seluruh data penempatan PKL aktif yang dibimbing oleh guru pembimbing
    private function getPenempatanAktif($guru)
    {
        return PenempatanPkl::withoutGlobalScope('aktif')
            ->with('siswa')
            ->where('guru_pembimbing_id', $guru->id)
            ->where('status', 'aktif')
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->get();
    }

    // Mengambil data penempatan PKL berdasarkan ID penempatan
    private function findPenempatan($penempatanId, $guru)
    {
        return PenempatanPkl::withoutGlobalScope('aktif')
            ->with('tempatPkl', 'siswa')
            ->where('id', $penempatanId)
            ->where('guru_pembimbing_id', $guru->id)
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->first();
    }

    // Mengambil seluruh data penilaian KPI yang telah divalidasi
    private function getAllKpi($penempatanId)
    {
        return PenilaianKpi::with('indikator.kategori')
            ->where('penempatan_pkl_id', $penempatanId)
            ->whereHas('penempatanPkl.siswa.user', fn($q) => $q->where('is_active', 1))
            ->where('status_validasi', 'diterima')
            ->get();
    }

    // Menentukan tanggal aktif serta navigasi ke tanggal sebelumnya dan berikutnya
    private function resolveTanggalNavigasi($penempatanId, ?string $requestedTanggal): array
    {
        $tanggalList = PenilaianKpi::where('penempatan_pkl_id', $penempatanId)
            ->whereHas('penempatanPkl.siswa.user', fn($q) => $q->where('is_active', 1))
            ->where('status_validasi', 'diterima')
            ->selectRaw('DATE(periode_penilaian) as tanggal')
            ->distinct()
            ->orderByDesc('tanggal')
            ->pluck('tanggal');

        $tanggalAktif = $requestedTanggal ?? ($tanggalList->first() ?? null);

        $index = $tanggalList->search($tanggalAktif);

        $prev = ($index !== false) ? ($tanggalList[$index + 1] ?? null) : null;
        $next = ($index !== false) ? ($tanggalList[$index - 1] ?? null) : null;

        return [$tanggalAktif, $prev, $next];
    }

    // Mengambil data penilaian KPI berdasarkan tanggal yang dipilih
    private function getKpiByTanggal($penempatanId, string $tanggal)
    {
        return PenilaianKpi::with('indikator.kategori')
            ->whereHas('penempatanPkl.siswa.user', fn($q) => $q->active())
            ->where('penempatan_pkl_id', $penempatanId)
            ->whereDate('periode_penilaian', $tanggal)
            ->where('status_validasi', 'diterima')
            ->get();
    }

    // Mengambil bobot setiap kategori KPI dari database
    private function getBobot(): array
    {
        $bobot = KpiKategori::pluck('bobot_persen', 'nama_kategori');

        return [
            'teknis'    => ($bobot['Aspek Teknis'] ?? 0) / 100,
            'nonTeknis' => ($bobot['Aspek Non-Teknis'] ?? 0) / 100,
            'presensi'  => ($bobot['Aspek Presensi'] ?? 0) / 100,
            'laporan'   => ($bobot['Aspek Laporan'] ?? 0) / 100,
        ];
    }

    // Menghitung nilai akhir KPI berdasarkan bobot setiap kategori penilaian
    private function hitungNilaiAkhir($semuaKpi, $penempatanModel, $penempatanId, array $bobot): array
    {
        $nilaiTeknis    = $this->hitungTeknis($semuaKpi);
        $nilaiNonTeknis = $this->hitungNonTeknis($semuaKpi);
        $nilaiPresensi  = $this->hitungPresensi($penempatanModel);
        $nilaiLaporan   = $this->hitungLaporan($penempatanId);

        $nilaiTeknisBobot    = round($nilaiTeknis * $bobot['teknis'], 2);
        $nilaiNonTeknisBobot = round($nilaiNonTeknis * $bobot['nonTeknis'], 2);
        $nilaiPresensiBobot  = round($nilaiPresensi * $bobot['presensi'], 2);
        $nilaiLaporanBobot   = round($nilaiLaporan * $bobot['laporan'], 2);

        $rataRata = round(
            $nilaiTeknisBobot + $nilaiNonTeknisBobot + $nilaiPresensiBobot + $nilaiLaporanBobot,
            2
        );

        return [$nilaiTeknisBobot, $nilaiNonTeknisBobot, $nilaiPresensiBobot, $nilaiLaporanBobot, $rataRata];
    }

    // Menghitung rata-rata nilai aspek teknis
    private function hitungTeknis($kpi)
    {
        $data = $kpi->filter(
            fn($i) => optional($i->indikator->kategori)->nama_kategori === 'Aspek Teknis'
        );

        return $data->count()
            ? round($data->avg('nilai'), 2)
            : 0;
    }

    // Menghitung rata-rata nilai aspek non-teknis
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

    // Menghitung nilai aspek presensi berdasarkan kehadiran siswa selama periode PKL
    private function hitungPresensi($penempatan)
    {
        $tanggalMulai  = Carbon::parse($penempatan->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($penempatan->tanggal_selesai);

        $batasAkhir = now()->lessThan($tanggalSelesai) ? now() : $tanggalSelesai;

        $hariWajib = collect($penempatan->tempatPkl->hari_wajib ?? [])
            ->map(fn($h) => strtolower(trim($h)));

        $mappingHari = [
            'monday'    => 'senin',
            'tuesday'   => 'selasa',
            'wednesday' => 'rabu',
            'thursday'  => 'kamis',
            'friday'    => 'jumat',
            'saturday'  => 'sabtu',
            'sunday'    => 'minggu',
        ];

        $tanggalWajib = $this->buildTanggalWajib($tanggalMulai, $batasAkhir, $hariWajib, $mappingHari);

        $presensi = Presensi::where('penempatan_pkl_id', $penempatan->id)
            ->whereHas('siswa.user', fn($q) => $q->where('is_active', 1))
            ->whereBetween('tanggal', [$tanggalMulai, $batasAkhir])
            ->get()
            ->keyBy(fn($p) => Carbon::parse($p->tanggal)->toDateString());

        $jamMasuk = $penempatan->tempatPkl->jam_masuk;
        $toleransi = $penempatan->tempatPkl->toleransi_keterlambatan ?? 0;

        $totalNilai = $this->hitungTotalNilaiPresensi($tanggalWajib, $presensi, $jamMasuk, $toleransi);

        return $tanggalWajib->count()
            ? round($totalNilai / $tanggalWajib->count(), 2)
            : 0;
    }

    // Menyusun daftar tanggal yang termasuk hari wajib PKL
    private function buildTanggalWajib(Carbon $mulai, Carbon $batas, $hariWajib, array $mapping)
    {
        $cursor = $mulai->copy();
        $tanggalWajib = collect();

        while ($cursor <= $batas) {
            $hari = $mapping[strtolower($cursor->format('l'))] ?? '';
            if ($hariWajib->contains($hari)) {
                $tanggalWajib->push($cursor->toDateString());
            }
            $cursor->addDay();
        }

        return $tanggalWajib;
    }

    // Menghitung total nilai presensi berdasarkan aturan penilaian presensi
    private function hitungTotalNilaiPresensi($tanggalWajib, $presensi, $jamMasuk, int $toleransi): int
    {
        $total = 0;

        foreach ($tanggalWajib as $tgl) {
            $p = $presensi->get($tgl);

            if (!$p) {
                continue;
            }

            if ($p->jenis_presensi === 'hadir' && $p->status_validasi === 'diterima') {
                $jamPresensi = Carbon::parse($p->waktu_presensi);
                $jamBatas    = Carbon::parse($jamMasuk)->addMinutes($toleransi);
                $total += $jamPresensi->gt($jamBatas) ? 80 : 100;
            } elseif ($p->jenis_presensi === 'izin' && $p->status_validasi === 'diterima') {
                $total += 85;
            } elseif ($p->jenis_presensi === 'libur' && $p->status_validasi === 'diterima') {
                $total += 100;
            } elseif ($p->jenis_presensi === 'terlambat') {
                $total += 80;
            }
        }

        return $total;
    }

    // Menghitung nilai aspek laporan berdasarkan persentase laporan yang telah divalidasi
    private function hitungLaporan($penempatanId)
    {
        $base = LaporanKegiatan::where('penempatan_pkl_id', $penempatanId)
            ->whereHas('penempatanPkl.siswa.user', fn($q) => $q->where('is_active', 1));

        $total = $base->count();
        $valid = (clone $base)->where('status_validasi', 'diterima')->count();

        return $total
            ? round(($valid / $total) * 100, 2)
            : 0;
    }
}