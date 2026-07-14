<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory;

    protected $table = 'aset';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'status',
        'tanggal_instalasi',
        'penanggungjawab',
    ];

    public function komponen()
    {
        return $this->hasMany(Komponen::class, 'kode_aset', 'kode_aset');
    }
}
