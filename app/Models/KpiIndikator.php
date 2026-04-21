<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiIndikator extends Model
{
    protected $table = 'kpi_indikator';

    protected $fillable = [
        'kpi_kategori_id',
        'nama_indikator',
        'bobot_persen',
        'deskripsi',
    ];

    public function kategori()
    {
        return $this->belongsTo(KpiKategori::class, 'kpi_kategori_id');
    }
}
