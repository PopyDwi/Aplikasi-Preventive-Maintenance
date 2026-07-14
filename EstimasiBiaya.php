<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimasiBiaya extends Model
{
    use HasFactory;

    protected $table = 'estimasi_biaya';

    protected $fillable = [
        'tanggal',
        'kode_aset',
        'kode_komponen',
        'total_downtime',
        'biaya_per_jam',
        'biaya_perbaikan',
        'total_estimasi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_downtime' => 'float',
        'biaya_per_jam' => 'decimal:2',
        'biaya_perbaikan' => 'decimal:2',
        'total_estimasi' => 'decimal:2',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_aset', 'kode_aset');
    }

    public function komponen()
    {
        return $this->belongsTo(Komponen::class, 'kode_komponen', 'kode_komponen');
    }
}
