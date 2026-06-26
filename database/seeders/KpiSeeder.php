<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiKategori;
use App\Models\KpiIndikator;

class KpiSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | KPI KATEGORI
        |--------------------------------------------------------------------------
        */

        $kategoriTeknis = KpiKategori::updateOrCreate(
            ['nama_kategori' => 'Aspek Teknis'],
            [
                'bobot_persen' => 40,
                'deskripsi' => 'Penilaian kemampuan teknis siswa selama PKL',
            ]
        );

        $kategoriNonTeknis = KpiKategori::updateOrCreate(
            ['nama_kategori' => 'Aspek Non-Teknis'],
            [
                'bobot_persen' => 20,
                'deskripsi' => 'Penilaian sikap dan perilaku siswa',
            ]
        );

        $kategoriPresensi = KpiKategori::updateOrCreate(
            ['nama_kategori' => 'Aspek Presensi'],
            [
                'bobot_persen' => 20,
                'deskripsi' => 'Penilaian berdasarkan kehadiran',
            ]
        );

        $kategoriLaporan = KpiKategori::updateOrCreate(
            ['nama_kategori' => 'Aspek Laporan'],
            [
                'bobot_persen' => 20,
                'deskripsi' => 'Penilaian laporan kegiatan PKL',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | ASPEK TEKNIS
        |--------------------------------------------------------------------------
        */

        $indikatorTeknis = [

            'Instalasi Hardware',

            'Instalasi Software',

            'Konfigurasi Jaringan',

            'Troubleshooting',

            'Maintenance Komputer',

        ];

        foreach ($indikatorTeknis as $nama) {

            KpiIndikator::updateOrCreate(

                [
                    'nama_indikator' => $nama,
                ],

                [
                    'kpi_kategori_id' => $kategoriTeknis->id,
                    'bobot_persen' => 0,
                    'deskripsi' => null,
                ]

            );

        }

        /*
        |--------------------------------------------------------------------------
        | ASPEK NON TEKNIS
        |--------------------------------------------------------------------------
        */

        $indikatorNonTeknis = [

            'Disiplin',

            'Ketertiban',

            'Kerajinan',

            'Kerjasama',

            'Inisiatif & Kreatif',

            'Tanggung Jawab',

            'Kemampuan Kerja',

            'Motivasi',

            'Kebersihan',

            'Kerapian',

            'Kualitas Kerja',

            'Sopan Santun',

        ];

        foreach ($indikatorNonTeknis as $nama) {

            KpiIndikator::updateOrCreate(

                [
                    'nama_indikator' => $nama,
                ],

                [
                    'kpi_kategori_id' => $kategoriNonTeknis->id,
                    'bobot_persen' => 0,
                    'deskripsi' => null,
                ]

            );

        }

        $this->command->info('KPI Seeder berhasil dijalankan.');
    }
}