<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;
use App\Models\PenempatanPkl;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    /**
     * Lokasi default foto
     */
    private string $fotoPresensi = 'storage/presensi/presensi.png';
    private string $buktiIzin = 'storage/izin/izin.png';

    /**
     * Jalankan Seeder
     */
    public function run(): void
    {
        $penempatan = PenempatanPkl::with('tempatPkl')
            ->where('status', 'aktif')
            ->get();

        foreach ($penempatan as $item) {

            /**
             * Presensi hanya dibuat
             * 1 Juni 2026 - 3 Juli 2026
             */
            $tanggal = Carbon::create(2026, 6, 1);

            $selesai = Carbon::create(2026, 7, 3);

            while ($tanggal->lte($selesai)) {

                /**
                 * Skip jika bukan hari wajib
                 */
                if (!$this->isHariWajib($tanggal, $item)) {
                    $tanggal->addDay();
                    continue;
                }

                /**
                 * Status Presensi
                 *
                 * Hadir = 82%
                 * Izin = 10%
                 * Alpha = 8%
                 */
                $status = $this->generateStatus();

                /**
                 * Nilai default
                 */
                $foto = null;
                $bukti = null;
                $keterangan = null;
                $latitude = null;
                $longitude = null;
                $jarak = null;

                /**
                 * Jika hadir
                 */
                if ($status === 'hadir') {

                    $foto = $this->fotoPresensi;

                    $latitude = $this->generateLatitude(
                        $item->tempatPkl->latitude
                    );

                    $longitude = $this->generateLongitude(
                        $item->tempatPkl->longitude
                    );

                    $jarak = rand(5, $item->tempatPkl->radius_meter - 5);

                    /**
                     * Jika izin
                     */
                } elseif ($status === 'izin') {

                    $bukti = $this->buktiIzin;

                    $keterangan = collect([
                        'Sakit',
                        'Izin keluarga',
                        'Keperluan sekolah',
                        'Kontrol kesehatan',
                        'Acara keluarga'
                    ])->random();
                }

                Presensi::updateOrCreate(

                    [
                        'penempatan_pkl_id' => $item->id,
                        'tanggal' => $tanggal->toDateString(),
                    ],

                    [

                        'jenis_presensi' => $status,

                        'waktu_presensi' => $status == 'alpha'
                            ? null
                            : $this->generateJamMasuk(),

                        'foto_presensi' => $foto,

                        'latitude' => $latitude,

                        'longitude' => $longitude,

                        'jarak_meter' => $jarak,

                        'keterangan_izin' => $status == 'izin'
                            ? $keterangan
                            : null,

                        'bukti_izin' => $bukti,

                        'status_validasi' => 'diterima',

                        'validated_by' => 1,

                        'validated_at' => now(),

                        'alasan_penolakan' => null,

                    ]

                );

                $tanggal->addDay();
            }
        }

        $this->command->info('Presensi Seeder selesai.');
    }

    /**
     * ======================================
     * Helper Function
     * ======================================
     */

    private function generateStatus(): string
    {
        $angka = rand(1, 100);

        if ($angka <= 82) {
            return 'hadir';
        }

        if ($angka <= 92) {
            return 'izin';
        }

        return 'alpha';
    }

    private function generateJamMasuk(): string
    {
        /**
         * Jam masuk realistis
         * 07:00 - 08:20
         */

        $jam = rand(7, 8);

        if ($jam == 7) {

            $menit = rand(0, 59);

        } else {

            $menit = rand(0, 20);

        }

        return sprintf('%02d:%02d:00', $jam, $menit);
    }

    private function generateLatitude($lat): float
    {
        return $lat + (rand(-20, 20) / 100000);
    }

    private function generateLongitude($lng): float
    {
        return $lng + (rand(-20, 20) / 100000);
    }

    private function isHariWajib(Carbon $tanggal, PenempatanPkl $penempatan): bool
    {
        $hari = strtolower($tanggal->locale('id')->dayName);

        $hariWajib = $penempatan->tempatPkl->hari_wajib ?? [];

        return in_array($hari, $hariWajib);
    }
}