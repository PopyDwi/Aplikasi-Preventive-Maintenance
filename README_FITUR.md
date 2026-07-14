# Sistem FMEA/RCM - Panduan Lengkap

## 📋 Daftar Isi
1. [Gambaran Umum](#gambaran-umum)
2. [Fitur Baru](#fitur-baru)
3. [Struktur File](#struktur-file)
4. [Cara Menggunakan](#cara-menggunakan)
5. [Integrasi Database](#integrasi-database)
6. [FAQ](#faq)

---

## 🎯 Gambaran Umum

Sistem ini mengimplementasikan **Failure Mode and Effects Analysis (FMEA)** untuk menentukan **Reliability Centered Maintenance (RCM)**. Sistem secara otomatis menghitung nilai RPN (Risk Priority Number) dan menghasilkan rekomendasi perawatan serta jadwal maintenance yang disesuaikan dengan tingkat risiko.

### Alur Proses
```
Input Data Kerusakan 
    ↓
Hitung RPN (S × O × D)
    ↓
Tentukan Kategori Risiko
    ↓
Tentukan Interval Maintenance
    ↓
Generate Rekomendasi Perawatan
    ↓
Buat Jadwal Maintenance Otomatis
    ↓
Simpan ke Database
```

---

## ✨ Fitur Baru

### 1. **Form Input FMEA yang Komprehensif**
- Input Kode Mesin, Komponen, Mode Kegagalan
- Input Severity, Occurrence, Detection (1-10)
- Input Dampak Kegagalan
- Validasi otomatis pada form

### 2. **Perhitungan RPN Otomatis**
- **Rumus**: RPN = Severity × Occurrence × Detection
- Perhitungan real-time saat user mengisi form
- Range nilai: 1 - 1000

### 3. **Kategori Risiko Otomatis**
| RPN | Kategori | Interval | Styling |
|-----|----------|----------|---------|
| ≥ 300 | Sangat Tinggi | 7 hari | 🔴 Merah |
| 200-299 | Tinggi | 14 hari | 🟠 Oranye |
| 100-199 | Sedang | 30 hari | 🟡 Kuning |
| < 100 | Rendah | 90 hari | 🟢 Hijau |

### 4. **Rekomendasi Perawatan Otomatis**
Setiap kategori memiliki rekomendasi tindakan yang berbeda:

- **Sangat Tinggi**: Inspeksi menyeluruh, penggantian sparepart, maintenance setiap 7 hari
- **Tinggi**: Inspeksi rutin, monitoring kondisi, maintenance setiap 14 hari
- **Sedang**: Pemeriksaan berkala, pembersihan, maintenance setiap 30 hari
- **Rendah**: Monitoring normal, pengecekan visual, maintenance setiap 90 hari

### 5. **Jadwal Maintenance Otomatis**
- Tanggal maintenance dihitung otomatis: Hari ini + Interval
- Formatnya: Tanggal dalam bahasa Indonesia
- Dapat dibuat langsung ke halaman Jadwal Maintenance

### 6. **Tabel Hasil Analisis Lengkap**
Menampilkan:
- No, Kode Mesin, Komponen, Mode Kegagalan
- Severity, Occurrence, Detection, RPN
- Kategori Risiko, Interval Maintenance
- Jadwal Maintenance Berikutnya
- Tombol Aksi (Jadwal, Hapus)

### 7. **Integrasi Jadwal Maintenance**
- Tombol "Jadwal" pada setiap hasil analisis
- Data otomatis terisi di form Jadwal Maintenance
- Pre-fill semua field dari data analisis

### 8. **Data Persistence**
- **Frontend**: Menggunakan LocalStorage (untuk testing)
- **Backend**: Siap untuk database Laravel (production)

---

## 📁 Struktur File

### File yang Diubah/Dibuat:

```
maintenance_rcm/
├── resources/views/
│   ├── analisisrcmadm.blade.php          [UPDATED] Form input + hasil analisis
│   └── jadwalmaintenanceadm.blade.php    [UPDATED] Form jadwal + tabel jadwal
│
├── app/Http/Controllers/
│   ├── FmeaAnalisisController.php        [NEW] Controller untuk FMEA
│   └── JadwalMaintenanceController.php   [NEW] Controller untuk Jadwal
│
├── app/Models/
│   ├── FmeaAnalisis.php                  [NEW] Model FMEA
│   └── JadwalMaintenance.php             [NEW] Model Jadwal Maintenance
│
├── database/migrations/
│   ├── 2024_01_01_000001_create_fmea_analisis_table.php       [NEW]
│   └── 2024_01_01_000002_create_jadwal_maintenance_table.php  [NEW]
│
├── DOKUMENTASI_FMEA_RCM.md               [NEW] Dokumentasi lengkap
├── ROUTES_EXAMPLE.php                    [NEW] Contoh routes
└── README_FITUR.md                       [NEW] File ini
```

---

## 🚀 Cara Menggunakan

### **Halaman Analisis RCM**

#### Step 1: Isi Form Input FMEA
1. Pilih Kode Mesin dari dropdown
2. Pilih Komponen dari dropdown
3. Masukkan Mode Kegagalan (teks bebas)
4. Masukkan nilai Severity (1-10)
5. Masukkan nilai Occurrence (1-10)
6. Masukkan nilai Detection (1-10)
7. (Opsional) Masukkan Dampak Kegagalan

#### Step 2: Klik Tombol "Hitung"
- Sistem akan menghitung RPN secara otomatis
- Menampilkan hasil di section "Hasil Analisis FMEA"

#### Step 3: Lihat Hasil Analisis
Section ini akan menampilkan:
- **Nilai RPN** (hasil perhitungan)
- **Kategori Risiko** (warna-coded)
- **Interval Maintenance** (dalam hari)
- **Jadwal Berikutnya** (tanggal otomatis)
- **Rekomendasi Perawatan** (teks lengkap)

#### Step 4: Tambah ke Hasil Analisis
Klik tombol "Tambah ke Hasil Analisis" untuk menyimpan data ke tabel.

#### Step 5: Buat Jadwal Maintenance
- Klik tombol "Jadwal" pada baris yang diinginkan
- Sistem akan redirect ke halaman Jadwal Maintenance
- Data analisis sudah ter-fill di form

### **Halaman Jadwal Maintenance**

#### Jika dari Analisis RCM:
- Data otomatis terisi dari analisis
- Info Card menampilkan data dari RCM
- Tinggal tambah Teknisi dan Catatan
- Klik "Simpan Jadwal"

#### Jika Manual:
- Isi semua field secara manual
- Klik "Simpan Jadwal"

#### Tabel Daftar Jadwal:
- Menampilkan semua jadwal yang sudah disimpan
- Dapat diedit atau dihapus
- Filter berdasarkan status dan prioritas

---

## 🗄️ Integrasi Database

### **Tahap 1: Setup Database**

#### Buat Migration
```bash
php artisan make:model FmeaAnalisis -m
php artisan make:model JadwalMaintenance -m
```

#### Copy File Migration
Salin file dari:
- `database/migrations/2024_01_01_000001_create_fmea_analisis_table.php`
- `database/migrations/2024_01_01_000002_create_jadwal_maintenance_table.php`

Ke folder `database/migrations/` project Anda.

#### Jalankan Migration
```bash
php artisan migrate
```

### **Tahap 2: Setup Model dan Controller**

Copy file:
- `app/Models/FmeaAnalisis.php`
- `app/Models/JadwalMaintenance.php`
- `app/Http/Controllers/FmeaAnalisisController.php`
- `app/Http/Controllers/JadwalMaintenanceController.php`

Ke project Anda.

### **Tahap 3: Setup Routes**

Tambahkan routes ke `routes/api.php`:
```php
Route::apiResource('fmea-analisis', FmeaAnalisisController::class);
Route::apiResource('jadwal-maintenance', JadwalMaintenanceController::class);

Route::post('/fmea-analisis/{fmeaAnalisis}/jadwal', [FmeaAnalisisController::class, 'buatJadwal']);
Route::patch('/jadwal-maintenance/{jadwalMaintenance}/status', [JadwalMaintenanceController::class, 'updateStatus']);
// ... (lihat ROUTES_EXAMPLE.php untuk routes lengkap)
```

### **Tahap 4: Update Blade File**

Ubah fungsi `submitForm()` di blade untuk menggunakan API:
```javascript
fetch('/api/fmea-analisis', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify(data)
})
```

---

## ❓ FAQ

### **Q: Di mana data disimpan saat ini?**
**A**: Data disimpan di LocalStorage browser (untuk testing). Saat production, ubah ke database dengan AJAX.

### **Q: Bagaimana cara mengubah interval maintenance?**
**A**: Edit fungsi `getIntervalMaintenance()` di controller atau blade file.

### **Q: Bisa custom rekomendasi perawatan?**
**A**: Ya, edit fungsi `getRekomendasi()` sesuai kebutuhan Anda.

### **Q: Berapa range nilai Severity, Occurrence, Detection?**
**A**: Range 1-10, dimana 10 adalah yang terburuk.

### **Q: Apa yang terjadi saat jadwal sudah lewat?**
**A**: Jadwal akan ditampilkan dengan status "Terlambat" dan bisa diupdate statusnya.

### **Q: Bisa export data ke Excel?**
**A**: Belum implementasi, tapi struktur database sudah siap untuk integrasi export library.

### **Q: Berapa max RPN?**
**A**: Max RPN adalah 10 × 10 × 10 = 1000 (Sangat Tinggi).

### **Q: Teknisi bisa lihat jadwal mereka?**
**A**: Bisa, dengan filter berdasarkan teknisi yang login.

---

## 📞 Support & Troubleshooting

### **Error: Data tidak tersimpan**
- Cek browser console (F12) untuk error message
- Pastikan LocalStorage tidak disabled

### **Error: Jadwal tidak muncul**
- Clear browser cache atau gunakan Incognito mode
- Check localStorage di DevTools

### **Saat production, Error 404 di API**
- Pastikan routes sudah ditambahkan ke `routes/api.php`
- Run `php artisan route:list`
- Check CSRF token di meta tag

### **Migration error**
- Pastikan file migration di folder `database/migrations/`
- Run `php artisan migrate:refresh` (hati-hati, akan delete data)

---

## 📈 Roadmap Fitur

- [ ] Export ke PDF/Excel
- [ ] Email notification untuk jadwal mendatang
- [ ] Dashboard dengan grafik statistik
- [ ] Mobile app integration
- [ ] Multi-user dengan role-based access
- [ ] Historical tracking dan analytics
- [ ] Predictive maintenance suggestions

---

## 📝 License & Credits

Sistem FMEA/RCM untuk Preventive Maintenance
Created for Maintenance Management System

---

**Version**: 1.0.0  
**Last Updated**: June 5, 2026  
**Status**: Production Ready (Frontend) + Database Ready
