<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kerusakan extends Model
{
    use HasFactory;

    protected $table = 'kerusakan';

    protected $fillable = [
        'tanggal_kerusakan',
        'kode_aset',
        'kode_komponen',
        'jenis_kerusakan',
        'deskripsi_kerusakan',
        'downtime_jam',
        'teknisi_pelapor',
        'user_id',
        'status',
        'estimasi_biaya',
        'catatan_teknisi',
        'foto_kerusakan',
    ];

    protected $appends = [
        'nama_aset',
        'nama_komponen',
        'teknisi_name',
    ];

    protected $casts = [
        'tanggal_kerusakan' => 'date',
        'downtime_jam' => 'float',
        'estimasi_biaya' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_aset', 'kode_aset');
    }

    public function komponen()
    {
        return $this->belongsTo(Komponen::class, 'kode_komponen', 'kode_komponen');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getNamaAsetAttribute()
    {
        return $this->aset?->nama_aset ?? $this->kode_aset;
    }

    public function getNamaKomponenAttribute()
    {
        return $this->komponen?->nama_komponen ?? $this->kode_komponen;
    }

    public function getTeknisiNameAttribute()
    {
        return $this->user?->name ?? $this->teknisi_pelapor;
    }
}
