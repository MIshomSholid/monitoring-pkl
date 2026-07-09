<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenempatanPkl;
use App\Models\PenilaianKpi;
use App\Models\KpiIndikator;
use Carbon\Carbon;

class PenilaianKpiSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus seluruh data KPI lama
        |--------------------------------------------------------------------------
        */

        PenilaianKpi::truncate();

        /*
        |--------------------------------------------------------------------------
        | Ambil indikator
        |--------------------------------------------------------------------------
        */

        $indikatorTeknis = KpiIndikator::where('kpi_kategori_id', 1)
            ->orderBy('id')
            ->get();

        $indikatorNonTeknis = KpiIndikator::where('kpi_kategori_id', 2)
            ->orderBy('id')
            ->get();

        if ($indikatorTeknis->isEmpty() || $indikatorNonTeknis->isEmpty()) {

            $this->command->error('Indikator KPI belum tersedia.');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Penempatan PKL
        |--------------------------------------------------------------------------
        */

        $penempatan = PenempatanPkl::with([
            'tempatPkl',
            'pembimbingLapangan',
        ])
            ->where('status', 'aktif')
            ->get();

        foreach ($penempatan as $item) {

            $tanggal = Carbon::parse($item->tanggal_mulai);

            $selesai = Carbon::parse(
                min($item->tanggal_selesai, '2026-07-10')
            );

            while ($tanggal->lte($selesai)) {

                /*
                |--------------------------------------------------------------------------
                | Hari wajib
                |--------------------------------------------------------------------------
                */

                if (!$this->isHariWajib($tanggal, $item)) {

                    $tanggal->addDay();

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Senin & Kamis
                |--------------------------------------------------------------------------
                */

                if (
                    !$tanggal->isMonday() &&
                    !$tanggal->isThursday()
                ) {

                    $tanggal->addDay();

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 1 Indikator Teknis
                |--------------------------------------------------------------------------
                */

                if ($indikatorTeknis->isNotEmpty()) {

                    $kpiTeknis = $indikatorTeknis->random();

                    PenilaianKpi::create([

                        'penempatan_pkl_id' => $item->id,

                        'kpi_indikator_id' => $kpiTeknis->id,

                        'nilai' => $this->generateNilai(),

                        'periode_penilaian' => $tanggal->toDateString(),

                        'status_validasi' => 'diterima',

                        'input_by' => optional($item->pembimbingLapangan)->user_id,

                        'validated_by' => 1,

                        'validated_at' => now(),

                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Semua Indikator Non Teknis
                |--------------------------------------------------------------------------
                */

                foreach ($indikatorNonTeknis as $kpi) {

                    PenilaianKpi::create([

                        'penempatan_pkl_id' => $item->id,

                        'kpi_indikator_id' => $kpi->id,

                        'nilai' => $this->generateNilai(),

                        'periode_penilaian' => $tanggal->toDateString(),

                        'status_validasi' => 'diterima',

                        'input_by' => optional($item->pembimbingLapangan)->user_id,

                        'validated_by' => 1,

                        'validated_at' => now(),

                    ]);

                }

                $tanggal->addDay();
            }
        }

        $this->command->info('Penilaian KPI Seeder berhasil dijalankan.');
    }

    /*
    |--------------------------------------------------------------------------
    | Hari Wajib
    |--------------------------------------------------------------------------
    */

    private function isHariWajib(Carbon $tanggal, PenempatanPkl $penempatan): bool
    {
        $hari = strtolower($tanggal->locale('id')->dayName);

        $hariWajib = $penempatan->tempatPkl->hari_wajib ?? [];

        return in_array($hari, $hariWajib);
    }

    /*
    |--------------------------------------------------------------------------
    | Nilai
    |--------------------------------------------------------------------------
    */

    private function generateNilai(): int
    {
        $angka = rand(1, 100);

        if ($angka <= 5) {
            return rand(80, 85);
        }

        if ($angka <= 25) {
            return rand(86, 90);
        }

        if ($angka <= 75) {
            return rand(91, 96);
        }

        return rand(97, 100);
    }

}