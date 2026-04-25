<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Siswa;
use App\Models\GuruPembimbing;
use App\Models\PembimbingLapangan;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    // ================= RELASI =================

    // Relasi ke data siswa
    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    // Relasi ke data guru pembimbing
    public function guruPembimbing()
    {
        return $this->hasOne(GuruPembimbing::class, 'user_id');
    }

    // (Opsional) Relasi ke pembimbing lapangan
    public function pembimbingLapangan()
    {
        return $this->hasOne(PembimbingLapangan::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
