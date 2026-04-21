<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nama_lengkap',
        'kelas',
        'jurusan',
        'no_telepon',
        'alamat',
        'foto_profil',
    ];

    /* ================= RELASI ================= */

    /**
     * Relasi ke akun user (login)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke seluruh penempatan PKL
     */
    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class, 'siswa_id');
    }

    /**
     * 🔑 Relasi utama: penempatan PKL yang AKTIF
     * Dipakai di:
     * - Profil
     * - Presensi
     * - KPI
     */
    public function penempatanAktif()
    {
        return $this->hasOne(PenempatanPkl::class, 'siswa_id')
            ->where('status', 'aktif');
    }
}
