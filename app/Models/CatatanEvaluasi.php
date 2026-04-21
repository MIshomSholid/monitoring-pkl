<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanEvaluasi extends Model
{
    protected $table = 'catatan_evaluasi';

    protected $fillable = [
        'penempatan_pkl_id',
        'kategori',
        'catatan',
        'tanggal',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public function penempatan()
    {
        return $this->belongsTo(PenempatanPkl::class, 'penempatan_pkl_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}