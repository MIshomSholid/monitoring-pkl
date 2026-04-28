<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'no_telepon',
        'alamat',
        'foto_profil',
    ];

    /**
     * =========================
     * RELATIONSHIP
     * =========================
     */

    // Relasi ke user (login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * =========================
     * ACCESSOR (OPSIONAL BIAR RAPI)
     * =========================
     */

    // Ambil URL foto profil (kalau kosong pakai default)
    public function getFotoUrlAttribute()
    {
        if ($this->foto_profil) {
            return asset('storage/' . $this->foto_profil);
        }

        return asset('images/default-user.png'); // pastikan file ini ada
    }
}