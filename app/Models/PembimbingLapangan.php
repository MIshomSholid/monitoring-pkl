<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PenempatanPkl;

class PembimbingLapangan extends Model
{
    protected $table = 'pembimbing_lapangan';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'jabatan',
        'no_telepon',
        'email_perusahaan',
        'foto_profil',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class, 'pembimbing_lapangan_id');
    }
}
