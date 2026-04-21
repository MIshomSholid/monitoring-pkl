<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PenempatanPkl;
use App\Models\Presensi;
use Carbon\Carbon;
use DB;

class GenerateAlphaPresensi extends Command
{
    protected $signature = 'presensi:generate-alpha';
    protected $description = 'Generate presensi ALPHA untuk siswa yang tidak presensi pada hari wajib';

    public function handle()
    {
        Carbon::setLocale('id');
        
        $tanggal = Carbon::yesterday();

        $namaHari = strtolower($tanggal->translatedFormat('l'));

        $penempatanList = PenempatanPkl::withoutGlobalScopes()
            ->with(['tempatPkl'])
            ->where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->get();

        $this->info("Tanggal generate: " . $tanggal);
        $this->info("Total PKL ditemukan: " . $penempatanList->count());

        foreach ($penempatanList as $pkl) {

            $hariWajib = $pkl->tempatPkl->hari_wajib ?? [];

            $hariWajib = array_map(function ($h) {
                return strtolower(trim($h));
            }, $hariWajib);

            if (!in_array($namaHari, $hariWajib)) {
                continue;
            }

            $sudahAda = Presensi::where('penempatan_pkl_id', $pkl->id)
                ->whereDate('tanggal', $tanggal)
                ->exists();

            if ($sudahAda) {
                continue;
            }

            Presensi::create([
                'penempatan_pkl_id' => $pkl->id,
                'tanggal' => $tanggal,
                'jenis_presensi' => 'alpha',
                'waktu_presensi' => null,
                'status_validasi' => 'diterima',
            ]);

            $this->info("Alpha dibuat: PKL {$pkl->id} - {$tanggal->format('Y-m-d')}");
        }

        return 0;
    }

}
