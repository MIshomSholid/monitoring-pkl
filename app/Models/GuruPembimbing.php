<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PenempatanPkl;

class GuruPembimbing extends Model
{
    protected $table = 'guru_pembimbing';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'mata_pelajaran',
        'no_telepon',
        'foto_profil',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class, 'guru_pembimbing_id');
    }
}
