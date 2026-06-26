<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\GuruPembimbing;
use App\Models\PembimbingLapangan;
use App\Models\TempatPkl;
use App\Models\PeriodePkl;
use App\Models\PenempatanPkl;

class PenempatanPklSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodePkl::firstOrFail();

        $tempat = TempatPkl::orderBy('id')->get()->values();
        $guru = GuruPembimbing::orderBy('id')->get()->values();
        $pembimbing = PembimbingLapangan::orderBy('id')->get()->values();
        $siswa = Siswa::orderBy('id')->get();

        foreach ($siswa as $index => $item) {

            // 1 perusahaan = 4 siswa
            $kelompok = intdiv($index, 4);

            PenempatanPkl::updateOrCreate(

                [
                    'siswa_id' => $item->id,
                    'periode_pkl_id' => $periode->id,
                ],

                [
                    'tempat_pkl_id' => $tempat[$kelompok]->id,

                    'tanggal_mulai' => '2026-06-01',

                    'tanggal_selesai' => '2026-08-31',

                    'guru_pembimbing_id' => $guru[$kelompok]->id,

                    'pembimbing_lapangan_id' => $pembimbing[$kelompok]->id,

                    'status' => 'aktif',

                    'status_pengajuan' => 'aktif',

                    'status_validasi' => 'diterima',

                    'validated_by' => 1,

                    'validated_at' => now(),
                ]
            );
        }

        $this->command->info('Penempatan PKL Seeder berhasil dijalankan.');
    }
}