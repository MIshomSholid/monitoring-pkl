<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;
use App\Models\LaporanKegiatan;
use Carbon\Carbon;

class LaporanKegiatanSeeder extends Seeder
{
    /**
     * File bukti default (Cloudinary)
     */
    private string $fileBukti = 'https://res.cloudinary.com/dl7px9jnw/image/upload/v1783628259/laporan-kegiatan/bnnhhmsxsd77qugh8cgl.jpg';

    /**
     * Jalankan Seeder
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus laporan lama pada rentang tanggal
        |--------------------------------------------------------------------------
        */

        LaporanKegiatan::whereBetween('tanggal', [
            Carbon::create(2026, 6, 1)->toDateString(),
            Carbon::create(2026, 7, 10)->toDateString(),
        ])->delete();

        /*
        |--------------------------------------------------------------------------
        | Ambil seluruh presensi HADIR
        |--------------------------------------------------------------------------
        */

        $presensiList = Presensi::with([
            'penempatanPkl.guruPembimbing',
        ])
            ->where('jenis_presensi', 'hadir')
            ->whereBetween('tanggal', [
                Carbon::create(2026, 6, 1)->toDateString(),
                Carbon::create(2026, 7, 10)->toDateString(),
            ])
            ->orderBy('penempatan_pkl_id')
            ->orderBy('tanggal')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Setiap presensi HADIR = 1 laporan kegiatan
        |--------------------------------------------------------------------------
        */

        foreach ($presensiList as $presensi) {

            $guruUserId = optional(
                $presensi->penempatanPkl->guruPembimbing
            )->user_id;

            LaporanKegiatan::create([

                'penempatan_pkl_id' => $presensi->penempatan_pkl_id,
                'tanggal'           => $presensi->tanggal,

                'deskripsi_kegiatan' => $this->generateDeskripsi(),

                'file_bukti' => $this->fileBukti,

                'status_validasi' => 'diterima',

                'catatan_validasi' => 'Laporan sesuai kegiatan PKL.',

                'validated_by' => $guruUserId,

                'validated_at' => now(),

            ]);
        }

        $this->command->info('Laporan Kegiatan Seeder berhasil dijalankan.');
    }

    /**
     * Contoh kegiatan
     */
    private function generateDeskripsi(): string
    {
        $kegiatan = [

            'Membantu instalasi sistem operasi pada komputer perusahaan.',
            'Melakukan perawatan dan pembersihan perangkat komputer.',
            'Membuat dokumentasi hasil pekerjaan harian.',
            'Membantu konfigurasi jaringan LAN di kantor.',
            'Melakukan pengecekan perangkat printer dan scanner.',
            'Membantu proses input data administrasi perusahaan.',
            'Melakukan backup data pada komputer server.',
            'Mengikuti briefing bersama pembimbing lapangan.',
            'Membantu troubleshooting komputer yang mengalami kendala.',
            'Melakukan instalasi aplikasi pendukung pekerjaan kantor.',
            'Membantu pengecekan koneksi internet perusahaan.',
            'Melakukan penggantian perangkat jaringan yang rusak.',
            'Membuat laporan pekerjaan harian kepada pembimbing.',
            'Melakukan pengecekan keamanan komputer dari virus.',
            'Membantu konfigurasi user dan printer sharing.',

        ];

        return $kegiatan[array_rand($kegiatan)];
    }
}