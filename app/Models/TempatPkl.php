<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempatPkl extends Model
{
    use HasFactory;

    protected $table = 'tempat_pkl';

    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'no_telepon',
        'email',
        'latitude',
        'longitude',
        'radius_meter',
        'kuota_siswa',
        'deskripsi',
        'jam_masuk',
        'toleransi_keterlambatan',
        'hari_wajib',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meter' => 'integer',
        'hari_wajib' => 'array',
    ];

    /* ================= RELASI (EXISTING + TAMBAHAN AMAN) ================= */

    /**
     * Relasi ke penempatan PKL
     * Dipakai oleh:
     * - Admin (rekap penempatan)
     * - Siswa (profil, presensi)
     */
    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class, 'tempat_pkl_id');
    }

    /* ================= HELPER (PENTING PRESENSI) ================= */

    /**
     * Cek apakah lokasi PKL valid
     */
    public function hasKoordinat()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Radius default (fallback)
     */
    public function getRadius()
    {
        return $this->radius_meter ?? 300;
    }

    public function sisaKuota()
    {
        $terpakai = $this->penempatanPkl()
            ->where('status', 'aktif')
            ->count();

        return $this->kuota_siswa - $terpakai;
    }

    public function terpakai()
    {
        return $this->penempatanPkl()
            ->where('status', 'aktif')
            ->count();
    }
}
