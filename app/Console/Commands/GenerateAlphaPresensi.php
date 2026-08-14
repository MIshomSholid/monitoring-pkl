<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PenempatanPkl;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DB;

class GenerateAlphaPresensi extends Command
{
    //php artisan presensi:generate-alpha
    protected $signature = 'presensi:generate-alpha';
    protected $description = 'Generate presensi ALPHA untuk siswa yang tidak presensi pada hari wajib';

    public function handle()
    {
        Log::info('GENERATE ALPHA JALAN: ' . now());
        Carbon::setLocale('id');

        $tanggal = Carbon::yesterday();
        //today
        $namaHari = strtolower($tanggal->translatedFormat('l'));

        $penempatanList = PenempatanPkl::withoutGlobalScopes()
            ->with([
                'tempatPkl',
                'siswa',
                'guruPembimbing',
                'pembimbingLapangan',
            ])
            ->where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->get();

        $this->info("Tanggal generate: " . $tanggal);
        $this->info("Total PKL ditemukan: " . $penempatanList->count());

        //Memproses setiap data penempatan PKL.
        foreach ($penempatanList as $pkl) {

            $hariWajib = $pkl->tempatPkl->hari_wajib ?? [];

            $hariWajib = array_map(function ($h) {
                return strtolower(trim($h));
            }, $hariWajib);

            //Jika hari sekarang bukan termasuk hari wajib
            if (!in_array($namaHari, $hariWajib)) {
                continue;
            }

            //Mengecek apakah siswa sudah memiliki presensi pada tanggal tersebut
            $sudahAda = Presensi::where('penempatan_pkl_id', $pkl->id)
                ->whereDate('tanggal', $tanggal)
                ->exists();

            if ($sudahAda) {
                continue;
            }

            //Membuat presensi otomatis dengan jenis alpha.
            $presensi = Presensi::create([
                'penempatan_pkl_id' => $pkl->id,
                'tanggal' => $tanggal,
                'jenis_presensi' => 'alpha',
                'waktu_presensi' => null,
                'status_validasi' => 'diterima',
            ]);

            //Mengirim data realtime ke Node.js
            Http::post(env('REALTIME_URL') . '/broadcast', [
                'event' => 'presensi.created',
                'data' => [
                    'id' => $presensi->id,
                    'tanggal' => $tanggal->locale('id')->translatedFormat('d M Y'),
                    'jenis_presensi' => 'alpha',
                    'waktu_presensi' => null,
                    'nama_siswa' => $pkl->siswa->nama_lengkap ?? 'Unknown',
                    'nama_perusahaan' => $pkl->tempatPkl->nama_perusahaan ?? '-',
                    'status_validasi' => 'diterima',
                    'jarak_meter' => null,
                    'foto_presensi' => null,
                    'bukti_izin' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'siswa_user_id' => $pkl->siswa->user_id ?? null,
                    'guru_user_id' => $pkl->guruPembimbing->user_id ?? null,
                    'pembimbing_user_id' => $pkl->pembimbingLapangan->user_id ?? null,
                ]
            ]);

            $this->info("Alpha dibuat: PKL {$pkl->id} - {$tanggal->format('Y-m-d')}");
        }

        return 0;
    }

}
