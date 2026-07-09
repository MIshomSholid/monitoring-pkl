<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;
use App\Models\PenempatanPkl;
use Carbon\Carbon;

class RegeneratePresensiTanggalSeeder extends Seeder
{
    private string $fotoPresensi = 'https://res.cloudinary.com/dl7px9jnw/image/upload/v1782668217/presensi/r2q6ptqvttfeeto4hkmr.jpg';

    private string $buktiIzin = 'https://res.cloudinary.com/dl7px9jnw/image/upload/v1782668990/izin/q8j5zbuxqv2coyrnb2xr.jpg';

    public function run(): void
    {
        $tanggal = Carbon::create(2026, 7, 10);

        // Hapus presensi tanggal tersebut
        Presensi::whereDate('tanggal', $tanggal)->delete();

        $penempatan = PenempatanPkl::with('tempatPkl')
            ->where('status', 'aktif')
            ->get();

        foreach ($penempatan as $item) {

            // Lewati jika bukan hari wajib
            if (!$this->isHariWajib($tanggal, $item)) {
                continue;
            }

            $status = $this->generateStatus();

            $foto = null;
            $bukti = null;
            $keterangan = null;
            $latitude = null;
            $longitude = null;
            $jarak = null;

            if ($status === 'hadir') {

                $foto = $this->fotoPresensi;

                $latitude = $this->generateLatitude(
                    $item->tempatPkl->latitude
                );

                $longitude = $this->generateLongitude(
                    $item->tempatPkl->longitude
                );

                $jarak = rand(5, $item->tempatPkl->radius_meter - 5);

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

            Presensi::create([

                'penempatan_pkl_id' => $item->id,

                'tanggal' => $tanggal->toDateString(),

                'jenis_presensi' => $status,

                'waktu_presensi' => $status === 'alpha'
                    ? null
                    : $this->generateJamMasuk(),

                'foto_presensi' => $foto,

                'latitude' => $latitude,

                'longitude' => $longitude,

                'jarak_meter' => $jarak,

                'keterangan_izin' => $status === 'izin'
                    ? $keterangan
                    : null,

                'bukti_izin' => $bukti,

                'status_validasi' => 'diterima',

                'validated_by' => 1,

                'validated_at' => now(),

                'alasan_penolakan' => null,
            ]);
        }

        $this->command->info('Presensi tanggal 10 Juli 2026 berhasil digenerate ulang.');
    }

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
        $jam = rand(7, 8);

        $menit = $jam == 7
            ? rand(0, 59)
            : rand(0, 20);

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