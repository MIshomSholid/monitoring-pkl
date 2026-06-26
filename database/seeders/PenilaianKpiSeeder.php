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
        | Ambil seluruh indikator
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
        | Ambil seluruh penempatan PKL aktif
        |--------------------------------------------------------------------------
        */

        $penempatan = PenempatanPkl::with([
            'tempatPkl',
            'pembimbingLapangan'
        ])
            ->where('status', 'aktif')
            ->get();

        foreach ($penempatan as $item) {

            /*
            |--------------------------------------------------------------------------
            | Timeline Seeder
            |--------------------------------------------------------------------------
            | Mulai 1 Juni 2026
            | Sampai 3 Juli 2026
            */

            $tanggal = Carbon::parse($item->tanggal_mulai);

            $selesai = Carbon::parse(
                min($item->tanggal_selesai, '2026-07-03')
            );

            while ($tanggal->lte($selesai)) {

                /*
                |--------------------------------------------------------------------------
                | Skip jika bukan hari wajib perusahaan
                |--------------------------------------------------------------------------
                */

                if (!$this->isHariWajib($tanggal, $item)) {

                    $tanggal->addDay();

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Penilaian hanya Senin & Kamis
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
                | ASPEK TEKNIS
                |--------------------------------------------------------------------------
                | Semua indikator teknis diisi
                */

                foreach ($indikatorTeknis as $kpi) {

                    PenilaianKpi::updateOrCreate(

                        [
                            'penempatan_pkl_id' => $item->id,
                            'kpi_indikator_id' => $kpi->id,
                            'periode_penilaian' => $tanggal->toDateString(),
                        ],

                        [
                            'nilai' => rand(75, 95),
                            'catatan' => $this->catatan(),
                            'status_validasi' => 'diterima',
                            'input_by' => optional($item->pembimbingLapangan)->user_id,
                            'validated_by' => 1,
                            'validated_at' => now(),
                        ]

                    );

                }

                /*
                |--------------------------------------------------------------------------
                | ASPEK NON TEKNIS
                |--------------------------------------------------------------------------
                */

                foreach ($indikatorNonTeknis as $kpi) {

                    PenilaianKpi::updateOrCreate(

                        [
                            'penempatan_pkl_id' => $item->id,
                            'kpi_indikator_id' => $kpi->id,
                            'periode_penilaian' => $tanggal->toDateString(),
                        ],

                        [
                            'nilai' => rand(75, 95),
                            'catatan' => $this->catatan(),
                            'status_validasi' => 'diterima',
                            'input_by' => optional($item->pembimbingLapangan)->user_id,
                            'validated_by' => 1,
                            'validated_at' => now(),
                        ]

                    );

                }

                $tanggal->addDay();

            }

        }

        $this->command->info('Penilaian KPI Seeder berhasil dijalankan.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Hari Wajib
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
    | Helper Catatan
    |--------------------------------------------------------------------------
    */

    private function catatan(): string
    {
        $catatan = [

            'Sangat baik.',
            'Disiplin dan bertanggung jawab.',
            'Aktif selama kegiatan PKL.',
            'Komunikasi sangat baik.',
            'Cepat memahami pekerjaan.',
            'Mampu bekerja sama dalam tim.',
            'Memiliki inisiatif yang baik.',
            'Perlu meningkatkan ketelitian.',
            'Performa kerja konsisten.',
            'Hasil pekerjaan memuaskan.',

        ];

        return $catatan[array_rand($catatan)];
    }
}