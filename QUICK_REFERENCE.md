# QUICK REFERENCE - Sistem FMEA/RCM

## 🎯 RUMUS CEPAT

```
RPN = Severity × Occurrence × Detection

Kategori Risiko:
- RPN ≥ 300   → Sangat Tinggi (Merah) → Interval: 7 hari
- RPN 200-299 → Tinggi (Oranye)      → Interval: 14 hari
- RPN 100-199 → Sedang (Kuning)      → Interval: 30 hari
- RPN < 100   → Rendah (Hijau)       → Interval: 90 hari
```

---

## 📱 PANDUAN PENGGUNA

### HALAMAN ANALISIS RCM

#### Langkah 1: Isi Form
```
1. Pilih Kode Mesin: CP 1 / CP 2 / CP 3 / CP 5 / WP 1
2. Pilih Komponen: Motor Listrik / Bearing / Impeller / Mechanical Seal / Wear Ring
3. Ketik Mode Kegagalan: mis. "Overheat" atau "Bearing Aus"
4. Masukkan Severity (1-10): mis. 8 (semakin tinggi = semakin serius)
5. Masukkan Occurrence (1-10): mis. 6 (semakin tinggi = semakin sering terjadi)
6. Masukkan Detection (1-10): mis. 5 (semakin tinggi = semakin sulit dideteksi)
7. (Opsional) Masukkan Dampak Kegagalan
```

#### Langkah 2: Klik "Hitung"
- Sistem otomatis menghitung RPN
- Menampilkan 4 kotak hasil
- Menampilkan rekomendasi perawatan

#### Langkah 3: Lihat Hasil Analisis
- **Nilai RPN**: Hasil perhitungan S × O × D
- **Kategori Risiko**: Sangat Tinggi / Tinggi / Sedang / Rendah
- **Interval Maintenance**: Berapa hari maintenance berikutnya
- **Jadwal Berikutnya**: Tanggal maintenance (otomatis dihitung)
- **Rekomendasi**: Tindakan perawatan yang harus dilakukan

#### Langkah 4: Simpan Hasil
Klik tombol "Tambah ke Hasil Analisis" untuk menyimpan ke tabel.

#### Langkah 5: Buat Jadwal
Klik tombol "Jadwal" pada baris yang ingin dijadwalkan → otomatis ke halaman Jadwal Maintenance.

---

### HALAMAN JADWAL MAINTENANCE

#### Jika Datang dari Analisis RCM:
1. Data otomatis terisi (Info Card tampil)
2. Tambahkan Teknisi Penanggung Jawab (opsional)
3. Tambahkan Catatan Tambahan (opsional)
4. Klik "Simpan Jadwal"

#### Jika Manual (tanpa dari Analisis):
1. Isi semua field form
2. Klik "Simpan Jadwal"

#### Tabel Jadwal:
- Lihat daftar semua jadwal yang sudah disimpan
- Klik "Edit" untuk mengubah
- Klik "Hapus" untuk menghapus

---

## 💾 DATA MANAGEMENT

### Hapus Data
- **Hasil Analisis**: Klik tombol "Hapus" di tabel
- **Jadwal Maintenance**: Klik tombol "Hapus" di tabel
- Konfirmasi sebelum hapus

### Edit Data
- **Hasil Analisis**: Belum bisa edit, harus hapus & buat baru
- **Jadwal Maintenance**: Klik "Edit", ubah, kemudian simpan

### Reset Form
Klik tombol "Batal" untuk mengosongkan semua field.

---

## 🔍 TROUBLESHOOTING

| Masalah | Penyebab | Solusi |
|---------|---------|--------|
| Data hilang saat refresh | LocalStorage cleared | Jangan clear browser cache |
| Jadwal tidak terisi di form | SessionStorage expired | Buka analisis → klik Jadwal kembali |
| RPN tidak berubah | Input belum lengkap | Isi semua field S, O, D |
| Kategori salah | Perhitungan error | Refresh halaman |
| Form tidak submit | Validasi gagal | Pastikan semua field terisi |

---

## 📊 CONTOH SKENARIO

### Skenario 1: Motor Listrik Overheat (KRITICAL)
```
Input:
- Kode Mesin: CP 2
- Komponen: Motor Listrik
- Mode Kegagalan: Overheat / Kumparan Terbakar
- Severity: 9 (sangat serius, produksi stop)
- Occurrence: 7 (sering terjadi)
- Detection: 6 (agak sulit dideteksi)

RPN = 9 × 7 × 6 = 378 ✓ SANGAT TINGGI

Result:
- Kategori: Sangat Tinggi (Merah)
- Interval: 7 hari
- Rekomendasi: Inspeksi menyeluruh, ganti komponen kritis
- Jadwal: Hari ini + 7 hari

Action:
- Klik "Jadwal" → Set teknisi → Simpan
```

### Skenario 2: Mechanical Seal Kebocoran (NORMAL)
```
Input:
- Kode Mesin: CP 1
- Komponen: Mechanical Seal
- Mode Kegagalan: Kebocoran Area Seal
- Severity: 6 (cukup serius)
- Occurrence: 5 (kadang terjadi)
- Detection: 4 (mudah dideteksi)

RPN = 6 × 5 × 4 = 120 ✓ SEDANG

Result:
- Kategori: Sedang (Kuning)
- Interval: 30 hari
- Rekomendasi: Pemeriksaan berkala, pembersihan
- Jadwal: Hari ini + 30 hari

Action:
- Monitor kondisi, jadwalkan maintenance rutin
```

---

## 🎨 WARNA & ARTI

```
🔴 SANGAT TINGGI (Merah)
   RPN ≥ 300
   Maintenance: Setiap 7 hari
   Action: URGENT - Segera lakukan inspeksi menyeluruh

🟠 TINGGI (Oranye)
   RPN 200-299
   Maintenance: Setiap 14 hari
   Action: PENTING - Jadwalkan maintenance rutin

🟡 SEDANG (Kuning)
   RPN 100-199
   Maintenance: Setiap 30 hari
   Action: NORMAL - Monitoring & maintenance berkala

🟢 RENDAH (Hijau)
   RPN < 100
   Maintenance: Setiap 90 hari
   Action: OK - Pengecekan rutin saja
```

---

## 🔐 DATA LOCATION

### LocalStorage Keys:
```javascript
// Hasil Analisis FMEA
localStorage.getItem('fmeaAnalisis')
// Output: Array of objects

// Jadwal Maintenance
localStorage.getItem('jadwalMaintenance')
// Output: Array of objects

// Contoh lihat di browser DevTools:
// F12 → Application → Local Storage → Select domain
```

---

## 📋 FORM FIELDS CHECKLIST

### Form Input FMEA:
- [ ] Kode Mesin (wajib diisi)
- [ ] Komponen (wajib diisi)
- [ ] Mode Kegagalan (wajib diisi)
- [ ] Severity 1-10 (wajib diisi)
- [ ] Occurrence 1-10 (wajib diisi)
- [ ] Detection 1-10 (wajib diisi)
- [ ] Dampak Kegagalan (opsional)

### Form Input Jadwal:
- [ ] Kode Mesin (wajib diisi)
- [ ] Komponen (wajib diisi)
- [ ] Tanggal Maintenance (wajib diisi)
- [ ] Prioritas (wajib diisi)
- [ ] Tindakan Maintenance (wajib diisi)
- [ ] Teknisi (opsional)
- [ ] Status (opsional, default: Dijadwalkan)
- [ ] Catatan (opsional)

---

## 🚀 SHORTCUT KEYS

| Action | Key |
|--------|-----|
| Submit Form | Enter (pada form) |
| Reset Form | Esc (pada form) |
| Delete Row | Klik "Hapus" |
| Edit Row | Klik "Edit" |
| New Schedule | Klik "Jadwal" |

---

## 📈 TIPS PENGGUNAAN

1. **Gunakan Severity tinggi untuk komponen critical**
   - Contoh: Motor listrik utama
   - Severity: 8-10

2. **Gunakan Occurrence tinggi jika kerusakan sering terjadi**
   - Contoh: Bearing yang sudah tua
   - Occurrence: 7-10

3. **Gunakan Detection tinggi jika sulit dideteksi**
   - Contoh: Korosi internal
   - Detection: 8-10

4. **Monitor tabel hasil analisis**
   - Prioritas pada RPN tinggi
   - Fokus pada kategori Sangat Tinggi

5. **Update jadwal maintenance**
   - Cek jadwal mendatang
   - Assign teknisi secepatnya
   - Monitor status maintenance

---

## 🔧 MAINTENANCE RECOMMENDATIONS

### SANGAT TINGGI - Action Poin
1. ✓ Inspeksi menyeluruh komponen
2. ✓ Cek getaran & suhu
3. ✓ Pelumasan berkala
4. ✓ Replacement sparepart jika perlu
5. ✓ Maintenance schedule: 7 hari

### TINGGI - Action Poin
1. ✓ Inspeksi rutin
2. ✓ Monitor kondisi mesin
3. ✓ Cek getaran & suhu
4. ✓ Maintenance schedule: 14 hari

### SEDANG - Action Poin
1. ✓ Pemeriksaan berkala
2. ✓ Pembersihan komponen
3. ✓ Cek fungsi normal
4. ✓ Maintenance schedule: 30 hari

### RENDAH - Action Poin
1. ✓ Monitoring normal
2. ✓ Pengecekan visual
3. ✓ Maintenance schedule: 90 hari

---

## 📱 TIPS MOBILE

- Gunakan landscape mode untuk form lengkap
- Table dapat di-scroll horizontal
- Button responsive untuk touch
- Data tetap tersimpan offline

---

## ❓ FREQUENTLY ASKED

**Q: Berapa max RPN?**
A: Max 1000 (10 × 10 × 10)

**Q: Berapa min RPN?**
A: Min 1 (1 × 1 × 1)

**Q: Bisa isi ulang dengan nilai sama?**
A: Ya, sistem akan update data lama

**Q: Data bisa restore?**
A: LocalStorage tidak ada restore, gunakan DB untuk production

**Q: Timezone untuk jadwal?**
A: Timezone lokal browser user

**Q: Support multi-language?**
A: Saat ini Bahasa Indonesia saja

**Q: Export data?**
A: Belum, tapi struktur ready untuk export

---

## 🎓 BELAJAR LEBIH LANJUT

**Untuk teknis lebih detail**, baca:
1. `DOKUMENTASI_FMEA_RCM.md` - Integrasi database
2. `README_FITUR.md` - Penjelasan fitur lengkap
3. `PERUBAHAN_RINGKASAN.md` - Apa yang berubah
4. `ROUTES_EXAMPLE.php` - API routes reference

---

**Last Updated**: June 5, 2026  
**Version**: 1.0.0
