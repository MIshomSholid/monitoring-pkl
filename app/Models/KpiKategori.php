<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiKategori extends Model
{
    protected $table = 'kpi_kategori';

    protected $fillable = [
        'nama_kategori',
        'bobot_persen',
        'deskripsi',
    ];

    public function indikator()
    {
        return $this->hasMany(KpiIndikator::class);
    }
}
