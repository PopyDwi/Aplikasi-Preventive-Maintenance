# Dokumentasi Sistem FMEA/RCM - Integrasi Database

## Gambaran Sistem

Sistem Analisis FMEA/RCM yang telah diupdate mendukung:
1. **Frontend** - Perhitungan RPN otomatis berbasis JavaScript
2. **LocalStorage** - Penyimpanan data sementara di browser
3. **Database** - Siap untuk integrasi dengan Laravel

## Alur Sistem

```
Data Kerusakan → Input Form FMEA → Hitung RPN → Kategori Risiko → 
Rekomendasi Perawatan → Jadwal Maintenance Otomatis → Simpan ke Database
```

## Rumus FMEA

**RPN = Severity × Occurrence × Detection**

### Kategori Risiko Berdasarkan RPN
- **RPN ≥ 300** → Sangat Tinggi
- **RPN 200-299** → Tinggi
- **RPN 100-199** → Sedang
- **RPN < 100** → Rendah

### Interval Maintenance Otomatis
- **Sangat Tinggi** → Setiap 7 hari
- **Tinggi** → Setiap 14 hari
- **Sedang** → Setiap 30 hari
- **Rendah** → Setiap 90 hari

## File-File yang Diupdate

### 1. Blade Template
- **resources/views/analisisrcmadm.blade.php** - Form input FMEA + hasil analisis
- **resources/views/jadwalmaintenanceadm.blade.php** - Form jadwal + tabel jadwal

### 2. Data Storage
- **Frontend**: LocalStorage (browser) - untuk testing
- **Backend**: Database Laravel (untuk production)

## Integrasi Database

### Step 1: Buat Migration

Jalankan perintah berikut untuk membuat migration:

```bash
php artisan make:model FmeaAnalisis -m
php artisan make:model JadwalMaintenance -m
```

### Step 2: Update Migration Files

**database/migrations/xxxx_xx_xx_create_fmea_analisis_table.php**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fmea_analisis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mesin');
            $table->string('komponen');
            $table->string('mode_kegagalan');
            $table->integer('severity'); // 1-10
            $table->integer('occurrence'); // 1-10
            $table->integer('detection'); // 1-10
            $table->integer('rpn'); // RPN value
            $table->string('kategori_risiko'); // Sangat Tinggi, Tinggi, Sedang, Rendah
            $table->string('interval_maintenance'); // Setiap X hari
            $table->text('dampak_kegagalan')->nullable();
            $table->text('rekomendasi_perawatan');
            $table->date('tanggal_jadwal_berikutnya');
            $table->timestamp('tanggal_input')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fmea_analisis');
    }
};
```

**database/migrations/xxxx_xx_xx_create_jadwal_maintenance_table.php**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_maintenance', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mesin');
            $table->string('komponen');
            $table->string('mode_kegagalan')->nullable();
            $table->string('kategori_risiko'); // Dari analisis FMEA
            $table->date('tanggal_maintenance');
            $table->string('teknisi_penanggungjawab')->nullable();
            $table->enum('prioritas', ['Sangat Tinggi', 'Tinggi', 'Sedang', 'Rendah']);
            $table->enum('status', ['Dijadwalkan', 'Pending', 'Proses', 'Selesai'])->default('Dijadwalkan');
            $table->text('tindakan_maintenance');
            $table->text('catatan_tambahan')->nullable();
            $table->integer('rpn')->nullable(); // RPN dari analisis
            $table->unsignedBigInteger('fmea_analisis_id')->nullable();
            $table->timestamps();
            
            $table->foreign('fmea_analisis_id')
                  ->references('id')
                  ->on('fmea_analisis')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_maintenance');
    }
};
```

### Step 3: Jalankan Migration

```bash
php artisan migrate
```

### Step 4: Update Model

**app/Models/FmeaAnalisis.php**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FmeaAnalisis extends Model
{
    protected $table = 'fmea_analisis';
    
    protected $fillable = [
        'kode_mesin',
        'komponen',
        'mode_kegagalan',
        'severity',
        'occurrence',
        'detection',
        'rpn',
        'kategori_risiko',
        'interval_maintenance',
        'dampak_kegagalan',
        'rekomendasi_perawatan',
        'tanggal_jadwal_berikutnya'
    ];

    protected $casts = [
        'tanggal_jadwal_berikutnya' => 'date',
        'tanggal_input' => 'datetime',
    ];

    // Relasi ke JadwalMaintenance
    public function jadwalMaintenance()
    {
        return $this->hasMany(JadwalMaintenance::class);
    }
}
```

**app/Models/JadwalMaintenance.php**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalMaintenance extends Model
{
    protected $table = 'jadwal_maintenance';
    
    protected $fillable = [
        'kode_mesin',
        'komponen',
        'mode_kegagalan',
        'kategori_risiko',
        'tanggal_maintenance',
        'teknisi_penanggungjawab',
        'prioritas',
        'status',
        'tindakan_maintenance',
        'catatan_tambahan',
        'rpn',
        'fmea_analisis_id'
    ];

    protected $casts = [
        'tanggal_maintenance' => 'date',
    ];

    // Relasi ke FmeaAnalisis
    public function fmeaAnalisis()
    {
        return $this->belongsTo(FmeaAnalisis::class);
    }
}
```

### Step 5: Buat Controller

**app/Http/Controllers/FmeaAnalisisController.php**
```php
<?php

namespace App\Http\Controllers;

use App\Models\FmeaAnalisis;
use App\Models\JadwalMaintenance;
use Illuminate\Http\Request;

class FmeaAnalisisController extends Controller
{
    // Fungsi helper untuk kategori risiko
    private function getKategoriRisiko($rpn)
    {
        if ($rpn >= 300) return 'Sangat Tinggi';
        if ($rpn >= 200) return 'Tinggi';
        if ($rpn >= 100) return 'Sedang';
        return 'Rendah';
    }

    // Fungsi helper untuk interval maintenance
    private function getIntervalMaintenance($kategori)
    {
        $intervals = [
            'Sangat Tinggi' => ['hari' => 7, 'text' => 'Setiap 7 hari'],
            'Tinggi' => ['hari' => 14, 'text' => 'Setiap 14 hari'],
            'Sedang' => ['hari' => 30, 'text' => 'Setiap 30 hari'],
            'Rendah' => ['hari' => 90, 'text' => 'Setiap 90 hari']
        ];
        return $intervals[$kategori] ?? $intervals['Rendah'];
    }

    // Fungsi helper untuk rekomendasi
    private function getRekomendasi($kategori)
    {
        $rekomendasi = [
            'Sangat Tinggi' => 'Segera lakukan inspeksi menyeluruh, pemeriksaan komponen kritis, pelumasan, penggantian sparepart jika diperlukan, dan jadwalkan preventive maintenance setiap 7 hari.',
            'Tinggi' => 'Lakukan inspeksi rutin, monitoring kondisi mesin, pemeriksaan getaran/suhu, dan jadwalkan preventive maintenance setiap 14 hari.',
            'Sedang' => 'Lakukan pemeriksaan berkala, pembersihan komponen, pengecekan fungsi mesin, dan jadwalkan preventive maintenance setiap 30 hari.',
            'Rendah' => 'Lakukan monitoring normal, pengecekan visual, dan jadwalkan preventive maintenance setiap 90 hari.'
        ];
        return $rekomendasi[$kategori] ?? $rekomendasi['Rendah'];
    }

    // Store analisis FMEA
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mesin' => 'required|string',
            'komponen' => 'required|string',
            'mode_kegagalan' => 'required|string',
            'severity' => 'required|integer|min:1|max:10',
            'occurrence' => 'required|integer|min:1|max:10',
            'detection' => 'required|integer|min:1|max:10',
            'dampak_kegagalan' => 'nullable|string',
        ]);

        // Hitung RPN
        $rpn = $validated['severity'] * $validated['occurrence'] * $validated['detection'];
        
        // Tentukan kategori
        $kategori = $this->getKategoriRisiko($rpn);
        
        // Dapatkan interval
        $interval = $this->getIntervalMaintenance($kategori);
        
        // Dapatkan rekomendasi
        $rekomendasi = $this->getRekomendasi($kategori);
        
        // Hitung jadwal berikutnya
        $tanggalJadwal = now()->addDays($interval['hari'])->toDateString();

        // Simpan ke database
        $analisis = FmeaAnalisis::create([
            'kode_mesin' => $validated['kode_mesin'],
            'komponen' => $validated['komponen'],
            'mode_kegagalan' => $validated['mode_kegagalan'],
            'severity' => $validated['severity'],
            'occurrence' => $validated['occurrence'],
            'detection' => $validated['detection'],
            'rpn' => $rpn,
            'kategori_risiko' => $kategori,
            'interval_maintenance' => $interval['text'],
            'dampak_kegagalan' => $validated['dampak_kegagalan'],
            'rekomendasi_perawatan' => $rekomendasi,
            'tanggal_jadwal_berikutnya' => $tanggalJadwal,
        ]);

        return response()->json([
            'success' => true,
            'data' => $analisis,
            'rpn' => $rpn,
            'kategori' => $kategori,
            'rekomendasi' => $rekomendasi,
            'jadwal' => $tanggalJadwal
        ]);
    }

    // Get semua analisis
    public function index()
    {
        return FmeaAnalisis::orderBy('created_at', 'desc')->get();
    }

    // Buat jadwal maintenance dari analisis
    public function buatJadwal(Request $request, FmeaAnalisis $fmeaAnalisis)
    {
        $validated = $request->validate([
            'teknisi_penanggungjawab' => 'nullable|string',
            'catatan_tambahan' => 'nullable|string',
        ]);

        $jadwal = JadwalMaintenance::create([
            'kode_mesin' => $fmeaAnalisis->kode_mesin,
            'komponen' => $fmeaAnalisis->komponen,
            'mode_kegagalan' => $fmeaAnalisis->mode_kegagalan,
            'kategori_risiko' => $fmeaAnalisis->kategori_risiko,
            'tanggal_maintenance' => $fmeaAnalisis->tanggal_jadwal_berikutnya,
            'teknisi_penanggungjawab' => $validated['teknisi_penanggungjawab'],
            'prioritas' => $fmeaAnalisis->kategori_risiko,
            'status' => 'Dijadwalkan',
            'tindakan_maintenance' => $fmeaAnalisis->rekomendasi_perawatan,
            'catatan_tambahan' => $validated['catatan_tambahan'],
            'rpn' => $fmeaAnalisis->rpn,
            'fmea_analisis_id' => $fmeaAnalisis->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $jadwal
        ]);
    }
}
```

**app/Http/Controllers/JadwalMaintenanceController.php**
```php
<?php

namespace App\Http\Controllers;

use App\Models\JadwalMaintenance;
use Illuminate\Http\Request;

class JadwalMaintenanceController extends Controller
{
    // Store jadwal maintenance
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mesin' => 'required|string',
            'komponen' => 'required|string',
            'tanggal_maintenance' => 'required|date',
            'prioritas' => 'required|string',
            'tindakan_maintenance' => 'required|string',
            'teknisi_penanggungjawab' => 'nullable|string',
            'status' => 'required|string',
            'catatan_tambahan' => 'nullable|string',
        ]);

        $jadwal = JadwalMaintenance::create($validated);

        return response()->json([
            'success' => true,
            'data' => $jadwal
        ]);
    }

    // Get semua jadwal
    public function index()
    {
        return JadwalMaintenance::orderBy('tanggal_maintenance')->get();
    }

    // Update status jadwal
    public function updateStatus(Request $request, JadwalMaintenance $jadwalMaintenance)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $jadwalMaintenance->update($validated);

        return response()->json([
            'success' => true,
            'data' => $jadwalMaintenance
        ]);
    }
}
```

### Step 6: Update Routes

Tambahkan ke **routes/web.php** atau buat API routes di **routes/api.php**:

```php
Route::post('/api/fmea-analisis', [FmeaAnalisisController::class, 'store']);
Route::get('/api/fmea-analisis', [FmeaAnalisisController::class, 'index']);
Route::post('/api/fmea-analisis/{fmeaAnalisis}/jadwal', [FmeaAnalisisController::class, 'buatJadwal']);

Route::post('/api/jadwal-maintenance', [JadwalMaintenanceController::class, 'store']);
Route::get('/api/jadwal-maintenance', [JadwalMaintenanceController::class, 'index']);
Route::put('/api/jadwal-maintenance/{jadwalMaintenance}/status', [JadwalMaintenanceController::class, 'updateStatus']);
```

## Transisi dari Frontend ke Backend

### Saat Ini (Frontend dengan LocalStorage)
- Data disimpan di LocalStorage browser
- Sempurna untuk testing dan development
- Data hilang saat browser cache dibersihkan

### Untuk Production (Dengan Database)
Ubah function `submitForm()` di blade untuk menggunakan AJAX:

```javascript
function submitForm() {
    // ... validasi ...
    
    fetch('/api/fmea-analisis', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            kode_mesin: kodeMesin,
            komponen: komponen,
            mode_kegagalan: modeKegagalan,
            severity: severity,
            occurrence: occurrence,
            detection: detection,
            dampak_kegagalan: dampakKegagalan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI dengan response
            updateHasilFromServer(data);
            updateHasilTable();
        }
    })
    .catch(error => console.error('Error:', error));
}
```

## Checklist Integrasi

- [ ] Jalankan `php artisan make:model FmeaAnalisis -m`
- [ ] Jalankan `php artisan make:model JadwalMaintenance -m`
- [ ] Update migration files sesuai dokumentasi
- [ ] Jalankan `php artisan migrate`
- [ ] Create Model files
- [ ] Create Controller files
- [ ] Update routes
- [ ] Update blade files untuk menggunakan AJAX ke backend
- [ ] Tambahkan CSRF token ke blade
- [ ] Test API endpoints
- [ ] Validasi data di backend

## Testing API dengan CURL/Postman

### Tambah Analisis FMEA
```bash
curl -X POST http://localhost:8000/api/fmea-analisis \
  -H "Content-Type: application/json" \
  -d '{
    "kode_mesin": "CP 1",
    "komponen": "Motor Listrik",
    "mode_kegagalan": "Overheat",
    "severity": 9,
    "occurrence": 7,
    "detection": 6,
    "dampak_kegagalan": "Produksi berhenti"
  }'
```

### Get Semua Analisis
```bash
curl http://localhost:8000/api/fmea-analisis
```

## Catatan Penting

1. **LocalStorage**: Data tersimpan di browser user, bukan di server
2. **Database**: Setelah setup database, data akan persistent di server
3. **Perhitungan**: RPN, kategori, interval, dan rekomendasi dihitung secara otomatis
4. **Jadwal**: Otomatis tercipta berdasarkan interval maintenance dari kategori risiko
5. **Status**: Default "Dijadwalkan" dapat diubah ke Pending, Proses, atau Selesai

## Support & Troubleshooting

- Pastikan database sudah terconfig di `.env`
- Pastikan migration sudah dijalankan
- Check Laravel logs jika ada error: `tail -f storage/logs/laravel.log`
- Verify API endpoints: `php artisan route:list`
