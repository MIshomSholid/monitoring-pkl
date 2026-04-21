<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformasiPkl extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'informasi_pkl';

    /**
     * Kolom yang boleh diisi (mass assignment)
     */
    protected $fillable = [
        'judul',
        'konten',
        'tipe',
        'tanggal_publish',
        'is_published',
        'created_by',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'is_published'    => 'boolean',
        'tanggal_publish' => 'date',
    ];

    /**
     * Relasi: Informasi dibuat oleh User (Admin)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
