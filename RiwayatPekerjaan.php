<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pekerjaan';

    protected $fillable = [
        'jadwal_maintenance_id',
        'kode_aset',
        'kode_komponen',
        'tanggal_pelaksanaan',
        'status_pekerjaan',
        'teknisi',
        'penanggungjawab',
        'nomor_whatsapp',
        'hasil_pengecekan',
        'tindakan_dilakukan',
        'durasi_pekerjaan',
        'biaya_maintenance',
        'dokumentasi'
    ];

    protected $appends = [
        'status',
        'tanggal_pekerjaan',
        'tindakan',
        'hasil_pekerjaan',
        'durasi_jam',
        'biaya'
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function normalizeStatus($status)
    {
        if ($status === null) {
            return null;
        }

        $mapping = [
            'Selesai' => 'Selesai',
        ];

        return $mapping[$status] ?? $status;
    }

    public function getStatusAttribute()
    {
        return $this->attributes['status_pekerjaan'] ?? null;
    }

    public function getTanggalPekerjaanAttribute()
    {
        return $this->attributes['tanggal_pelaksanaan'] ?? null;
    }

    public function getTindakanAttribute()
    {
        return $this->attributes['tindakan_dilakukan'] ?? null;
    }

    public function getHasilPekerjaanAttribute()
    {
        return $this->attributes['hasil_pengecekan'] ?? null;
    }

    public function getDurasiJamAttribute()
    {
        return $this->attributes['durasi_pekerjaan'] ?? null;
    }

    public function getBiayaAttribute()
    {
        return $this->attributes['biaya_maintenance'] ?? null;
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalMaintenance::class, 'jadwal_maintenance_id');
    }
}
