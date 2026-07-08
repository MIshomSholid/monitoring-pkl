<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\PenempatanPkl;
use App\Models\PenilaianKpi;
use App\Models\KpiIndikator;

class RegenerateKpiTanggalSeeder extends Seeder
{
    /**
     * ======================================
     * GANTI TANGGAL DISINI
     * ======================================
     */
    private string $tanggal = '2026-07-06';

    /**
     * ======================================
     * ASPEK TEKNIS
     * ======================================
     * Ganti nama indikator sesuai kebutuhan.
     * Kosongkan array jika tidak ingin membuat aspek teknis.
     */
    private array $aspekTeknis = [

        'Pemrograman Laravel',

        'Database MySQL',

        'REST API',

        'UI / UX',

        'Git & GitHub',

    ];

    public function run(): void
    {
        $tanggal = Carbon::parse($this->tanggal);

        /**
         * Hapus KPI tanggal tersebut
         */
        PenilaianKpi::whereDate('periode_penilaian', $tanggal)
            ->delete();

        /**
         * Penempatan PKL aktif
         */
        $penempatanList = PenempatanPkl::with([
            'pembimbingLapangan',
            'periode',
        ])
            ->where('status', 'aktif')
            ->get();

        /**
         * Indikator Non Teknis
         */
        $indikatorNonTeknis = KpiIndikator::where('kpi_kategori_id', 2)
            ->orderBy('id')
            ->get();

        /**
         * Indikator Teknis
         */
        $indikatorTeknis = collect();

        foreach ($this->aspekTeknis as $namaIndikator) {

            $indikatorTeknis->push(

                KpiIndikator::firstOrCreate(

                    [
                        'nama_indikator' => $namaIndikator,
                    ],

                    [
                        'kpi_kategori_id' => 1,
                        'bobot_persen' => 0,
                        'deskripsi' => null,
                    ]

                )

            );

        }

        foreach ($penempatanList as $penempatan) {

            if (!$penempatan->pembimbingLapangan) {
                continue;
            }

            /**
             * ===============================
             * NON TEKNIS
             * ===============================
             */
            foreach ($indikatorNonTeknis as $indikator) {

                PenilaianKpi::create([

                    'penempatan_pkl_id' => $penempatan->id,

                    'kpi_indikator_id' => $indikator->id,

                    'nilai' => $this->generateNilai(),

                    'periode_penilaian' => $tanggal->toDateString(),

                    'catatan' => null,

                    'status_validasi' => 'diterima',

                    'input_by' => $penempatan
                        ->pembimbingLapangan
                        ->user_id,

                    'validated_by' => 1,

                    'validated_at' => now(),

                ]);

            }

            /**
             * ===============================
             * TEKNIS
             * ===============================
             * Hanya 1 indikator teknis setiap hari.
             */
            if ($indikatorTeknis->isNotEmpty()) {

                $indikator = $indikatorTeknis->random();

                PenilaianKpi::create([

                    'penempatan_pkl_id' => $penempatan->id,

                    'kpi_indikator_id' => $indikator->id,

                    'nilai' => $this->generateNilai(),

                    'periode_penilaian' => $tanggal->toDateString(),

                    'catatan' => null,

                    'status_validasi' => 'diterima',

                    'input_by' => $penempatan
                        ->pembimbingLapangan
                        ->user_id,

                    'validated_by' => 1,

                    'validated_at' => now(),

                ]);

            }

        }

        $this->command->info('==============================================');
        $this->command->info('Regenerate KPI berhasil.');
        $this->command->info('Tanggal : ' . $tanggal->format('d-m-Y'));
        $this->command->info('Jumlah Siswa : ' . $penempatanList->count());
        $this->command->info('Indikator Non Teknis : ' . $indikatorNonTeknis->count());
        $this->command->info('Indikator Teknis : ' . $indikatorTeknis->count());
        $this->command->info('==============================================');
    }

    /**
     * ======================================
     * GENERATE NILAI REALISTIS
     * ======================================
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