<?php

namespace App\Http\Controllers\GuruPembimbing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\GuruPembimbing;
use App\Models\PenempatanPkl;
use App\Models\Presensi;
use App\Models\LaporanKegiatan;
use App\Models\PenilaianKpi;
use App\Models\KpiKategori;

use Carbon\Carbon;

class RekapSiswaController extends Controller
{

    public function index(Request $request)
    {
        $guru = GuruPembimbing::where('user_id', Auth::id())->firstOrFail();

        /* ================= LIST SISWA ================= */

        $daftarSiswa = PenempatanPkl::with('siswa')
            ->where('guru_pembimbing_id', $guru->id)
            ->get()
            ->pluck('siswa');

        $penempatan = collect();
        $presensi = collect();

        /* ================= JIKA SISWA DIPILIH ================= */

        if ($request->filled('siswa_id')) {

            $penempatanModel = PenempatanPkl::with([
                'siswa',
                'tempatPkl'
            ])
            ->where('guru_pembimbing_id', $guru->id)
            ->where('siswa_id', $request->siswa_id)
            ->first();

            if ($penempatanModel) {

                /* ================= PRESENSI PAGINATION ================= */

                $presensi = Presensi::where('penempatan_pkl_id', $penempatanModel->id)
                    ->orderBy('tanggal','desc')
                    ->paginate(10)
                    ->withQueryString();

                $penempatan = collect([
                    $this->prosesRekap($penempatanModel)
                ]);
            }
        }

        return view(
            'dashboard.guru-dashboard.rekap-siswa.index',
            compact('penempatan','daftarSiswa','presensi')
        );
    }


    /* =====================================================
        PROSES REKAP SISWA
    ===================================================== */

    private function prosesRekap($p)
    {

        $hadir = Presensi::where('penempatan_pkl_id',$p->id)
            ->where('jenis_presensi','hadir')->count();

        $izin = Presensi::where('penempatan_pkl_id',$p->id)
            ->where('jenis_presensi','izin')->count();

        $alpha = Presensi::where('penempatan_pkl_id',$p->id)
            ->where('jenis_presensi','alpha')->count();

        $nilaiPresensi = $this->hitungPresensi($p);


        /* ================= LAPORAN ================= */

        $laporan = [
            'diterima' => LaporanKegiatan::where('penempatan_pkl_id',$p->id)
                ->where('status_validasi','diterima')->count(),

            'menunggu' => LaporanKegiatan::where('penempatan_pkl_id',$p->id)
                ->where('status_validasi','menunggu')->count(),

            'ditolak' => LaporanKegiatan::where('penempatan_pkl_id',$p->id)
                ->where('status_validasi','ditolak')->count(),
        ];


        /* ================= KPI ================= */

        $bobot = KpiKategori::pluck('bobot_persen','nama_kategori');

        $bobotTeknis = ($bobot['Aspek Teknis'] ?? 0) / 100;
        $bobotNonTeknis = ($bobot['Aspek Non-Teknis'] ?? 0) / 100;
        $bobotPresensi = ($bobot['Aspek Presensi'] ?? 0) / 100;
        $bobotLaporan = ($bobot['Aspek Laporan'] ?? 0) / 100;

        $kpi = PenilaianKpi::with('indikator.kategori')
            ->where('penempatan_pkl_id',$p->id)
            ->where('status_validasi','diterima')
            ->get();

        $nilaiTeknis = $kpi
            ->filter(fn($i) => optional($i->indikator->kategori)->nama_kategori === 'Aspek Teknis')
            ->avg('nilai');

        $nilaiNonTeknis = $kpi
            ->filter(fn($i) => optional($i->indikator->kategori)->nama_kategori === 'Aspek Non-Teknis')
            ->groupBy('periode_penilaian')
            ->map(fn($items) => $items->avg('nilai'))
            ->avg();


        /* ================= NILAI LAPORAN ================= */

        $totalLaporan = $laporan['diterima'] + $laporan['menunggu'] + $laporan['ditolak'];

        $nilaiLaporan = $totalLaporan > 0
            ? round(($laporan['diterima'] / $totalLaporan) * 100,2)
            : 0;


        /* ================= NILAI AKHIR ================= */

        $nilaiAkhir = round(
            ($nilaiTeknis * $bobotTeknis) +
            ($nilaiNonTeknis * $bobotNonTeknis) +
            ($nilaiPresensi * $bobotPresensi) +
            ($nilaiLaporan * $bobotLaporan),
            2
        );

        return [
            'penempatan' => $p,
            'hadir' => $hadir,
            'izin' => $izin,
            'alpha' => $alpha,
            'laporan' => $laporan,
            'kpi' => $nilaiAkhir
        ];
    }


    /* =====================================================
        HITUNG NILAI PRESENSI
    ===================================================== */

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
            'monday'=>'senin',
            'tuesday'=>'selasa',
            'wednesday'=>'rabu',
            'thursday'=>'kamis',
            'friday'=>'jumat',
            'saturday'=>'sabtu',
            'sunday'=>'minggu'
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

        $presensi = Presensi::where('penempatan_pkl_id',$penempatan->id)
            ->whereBetween('tanggal',[$tanggalMulai,$batasAkhir])
            ->get()
            ->keyBy(fn($i)=>Carbon::parse($i->tanggal)->toDateString());

        $jamMasuk = $penempatan->tempatPkl->jam_masuk;
        $toleransi = $penempatan->tempatPkl->toleransi_keterlambatan ?? 0;

        $totalNilai = 0;

        foreach ($tanggalWajib as $tgl) {

            $p = $presensi->get($tgl);

            if (!$p) continue;

            if ($p->jenis_presensi === 'hadir' && $p->status_validasi === 'diterima') {

                $jamPresensi = Carbon::parse($p->waktu_presensi);
                $jamBatas = Carbon::parse($jamMasuk)->addMinutes($toleransi);

                $totalNilai += $jamPresensi->gt($jamBatas) ? 80 : 100;

            } 
            elseif ($p->jenis_presensi === 'izin' && $p->status_validasi === 'diterima') {

                $totalNilai += 85;

            } 
            elseif ($p->jenis_presensi === 'libur' && $p->status_validasi === 'diterima') {

                $totalNilai += 100;
            }
        }

        return $tanggalWajib->count()
            ? round($totalNilai / $tanggalWajib->count(),2)
            : 0;
    }
}