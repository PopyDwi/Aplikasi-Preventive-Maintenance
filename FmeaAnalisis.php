<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Aset;
use App\Models\Komponen;
use App\Models\JadwalMaintenance;

class FmeaAnalisis extends Model
{
    use HasFactory;

    protected $table = 'analisis_rcm';

    protected $fillable = [
        'kode_aset',
        'kode_komponen',
        'mode_kegagalan',
        'dampak_kegagalan',
        'severity',
        'occurrence',
        'detection',
        'rpn',
        'kategori_risiko',
        'rekomendasi_perawatan',
        'jadwal_maintenance_berikutnya',
    ];

    protected $casts = [
        'jadwal_maintenance_berikutnya' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'nama_aset',
        'nama_komponen',
    ];

    public function komponenRel()
    {
        return $this->belongsTo(Komponen::class, 'kode_komponen', 'kode_komponen');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_aset', 'kode_aset');
    }

    public function komponen()
    {
        return $this->belongsTo(Komponen::class, 'kode_komponen', 'kode_komponen');
    }

    /**
     * Relasi one-to-many dengan JadwalMaintenance
     */
    public function jadwalMaintenance()
    {
        return $this->hasMany(JadwalMaintenance::class, 'analisis_rcm_id', 'id');
    }

    protected static function booted()
    {
        static::deleting(function (FmeaAnalisis $analisis) {
            $analisis->jadwalMaintenance()->delete();
        });
    }

    public function getNamaAsetAttribute()
    {
        return $this->aset?->nama_aset ?? $this->kode_aset;
    }

    public function getNamaKomponenAttribute()
    {
        return $this->komponen?->nama_komponen ?? $this->kode_komponen;
    }

    /**
     * Accessor untuk kategori risiko dengan styling
     */
    public function getKategoriStyleAttribute()
    {
        $styles = [
            'Sangat Tinggi' => 'sangat-tinggi',
            'Tinggi' => 'tinggi',
            'Sedang' => 'sedang',
            'Rendah' => 'rendah'
        ];
        return $styles[$this->kategori_risiko] ?? 'rendah';
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_risiko', $kategori);
    }

    /**
     * Scope untuk filter berdasarkan aset
     */
    public function scopeByAset($query, $kodeAset)
    {
        return $query->where('kode_aset', $kodeAset);
    }

    /**
     * Scope untuk filter berdasarkan komponen
     */
    public function scopeByKomponen($query, $kodeKomponen)
    {
        return $query->where('kode_komponen', $kodeKomponen);
    }

    /**
     * Scope untuk yang belum memiliki jadwal
     */
    public function scopeTanpaJadwal($query)
    {
        return $query->whereDoesntHave('jadwalMaintenance', function ($q) {
            $q->where('status_jadwal', 'Dijadwalkan');
        });
    }

    /**
     * Scope untuk RPN tinggi (Sangat Tinggi + Tinggi)
     */
    public function scopeRpnTinggi($query)
    {
        return $query->where('rpn', '>=', 200);
    }
}