<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komponen extends Model
{
    use HasFactory;

    protected $table = 'komponen';

    protected $fillable = [
        'kode_komponen',
        'kode_aset',
        'nama_komponen',
        'fungsi_keterangan',
        'volume',
        'satuan',
        'harga_satuan',
        'total_biaya',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_aset', 'kode_aset');
    }
}
