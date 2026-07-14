<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class JadwalMaintenance extends Model
{
    use HasFactory;

    protected $table = 'jadwal_maintenance';

    protected $fillable = [
        'analisis_rcm_id',
        'kode_aset',
        'kode_komponen',
        'tanggal_maintenance',
        'prioritas',
        'status_jadwal',
        'tindakan_maintenance',
        'catatan_tambahan',
        'mode_kegagalan',
        'kategori_risiko',
        'rpn',
        'penanggungjawab',
        'nomor_whatsapp',
    ];

    protected $appends = [
        'nama_aset',
        'nama_komponen',
    ];

    protected $casts = [
        'tanggal_maintenance' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke FmeaAnalisis
     */
    public function fmeaAnalisis()
    {
        return $this->belongsTo(FmeaAnalisis::class, 'analisis_rcm_id', 'id');
    }

    /**
     * Relasi ke Aset
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'kode_aset', 'kode_aset');
    }

    /**
     * Relasi ke Komponen
     */
    public function komponen()
    {
        return $this->belongsTo(Komponen::class, 'kode_komponen', 'kode_komponen');
    }

    /**
     * Accessor untuk nama aset
     */
    public function getNamaAsetAttribute()
    {
        if ($this->relationLoaded('aset') && $this->aset) {
            return $this->aset->nama_aset;
        }
        return $this->kode_aset ?? '-';
    }

    /**
     * Accessor untuk nama komponen
     */
    public function getNamaKomponenAttribute()
    {
        if ($this->relationLoaded('komponen') && $this->komponen) {
            return $this->komponen->nama_komponen;
        }
        return $this->kode_komponen ?? '-';
    }

    /**
     * Accessor untuk status_jadwal (backward compatibility)
     */
    public function getStatusJadwalAttribute()
    {
        return $this->attributes['status_jadwal'] ?? null;
    }

    /**
     * Mutator untuk status_jadwal (backward compatibility)
     */
    public function setStatusJadwalAttribute($value)
    {
        $this->attributes['status_jadwal'] = $value;
    }

    /**
     * Accessor untuk status styling
     */
    public function getStatusStyleAttribute()
    {
        $styles = [
            'Dijadwalkan' => 'belum-dikerjaan',
            'Selesai' => 'selesai',
        ];
        return $styles[$this->status_jadwal ?? ($this->attributes['status_jadwal'] ?? null)] ?? 'belum-dikerjaan';
    }

    /**
     * Accessor untuk prioritas styling
     */
    public function getPrioritasStyleAttribute()
    {
        $styles = [
            'Sangat Tinggi' => 'sangat-tinggi',
            'Tinggi' => 'tinggi',
            'Sedang' => 'sedang',
            'Rendah' => 'rendah'
        ];
        return $styles[$this->prioritas] ?? 'rendah';
    }

    /**
     * Cek apakah jadwal sudah lewat
     */
    public function isOverdue()
    {
        return $this->tanggal_maintenance < Carbon::today() && ($this->status_jadwal ?? $this->attributes['status_jadwal'] ?? null) !== 'Selesai';
    }

    /**
     * Cek apakah jadwal akan datang dalam N hari
     */
    public function isUpcoming($days = 7)
    {
        $today = Carbon::today();
        $future = Carbon::today()->addDays($days);
        return $this->tanggal_maintenance->between($today, $future) && ($this->status_jadwal ?? $this->attributes['status_jadwal'] ?? null) !== 'Selesai';
    }

    /**
     * Dapatkan berapa hari sampai maintenance
     */
    public function getDaysUntilAttribute()
    {
        return $this->tanggal_maintenance->diffInDays(Carbon::today(), false);
    }

    /**
     * Scope untuk jadwal yang belum selesai
     */
    public function scopeAktif($query)
    {
        return $query->where('status_jadwal', '!=', 'Selesai');
    }

    /**
     * Scope untuk jadwal yang sudah lewat
     */
    public function scopeTerlambat($query)
    {
        return $query->where('tanggal_maintenance', '<', Carbon::today())
            ->where('status_jadwal', '!=', 'Selesai');
    }

    /**
     * Scope untuk jadwal yang akan datang
     */
    public function scopeMendatang($query, $days = 7)
    {
        return $query->whereBetween('tanggal_maintenance', [
                Carbon::today(),
                Carbon::today()->addDays($days)
            ])
            ->where('status_jadwal', '!=', 'Selesai');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_jadwal', $status);
    }

    /**
     * Scope untuk filter berdasarkan prioritas
     */
    public function scopeByPrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }

    /**
     * Scope untuk filter berdasarkan mesin
     */
    public function scopeByMesin($query, $kodeMesin)
    {
        return $query->where('kode_aset', $kodeMesin);
    }

    /**
     * Scope untuk filter berdasarkan penanggungjawab
     */
    public function scopeByPenanggungJawab($query, $penanggungJawab)
    {
        return $query->where('penanggungjawab', $penanggungJawab);
    }
}
