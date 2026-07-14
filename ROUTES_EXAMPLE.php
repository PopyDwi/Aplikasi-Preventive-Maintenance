// ============================================
// ROUTES UNTUK SISTEM FMEA/RCM
// ============================================
// 
// Tambahkan routes berikut ke routes/api.php atau routes/web.php
// Jika menggunakan API, pastikan sudah import Controller yang diperlukan
//

// ============================================
// ROUTES UNTUK ANALISIS FMEA
// ============================================

// GET - Menampilkan semua analisis FMEA
Route::get('/api/fmea-analisis', [FmeaAnalisisController::class, 'index']);
// Contoh: GET /api/fmea-analisis

// POST - Menyimpan analisis FMEA baru
Route::post('/api/fmea-analisis', [FmeaAnalisisController::class, 'store']);
// Contoh: POST /api/fmea-analisis dengan body JSON:
// {
//   "kode_mesin": "CP 1",
//   "komponen": "Motor Listrik",
//   "mode_kegagalan": "Overheat",
//   "severity": 9,
//   "occurrence": 7,
//   "detection": 6,
//   "dampak_kegagalan": "Produksi terhenti"
// }

// GET - Menampilkan detail analisis tertentu
Route::get('/api/fmea-analisis/{fmeaAnalisis}', [FmeaAnalisisController::class, 'show']);

// PUT - Memperbarui analisis FMEA
Route::put('/api/fmea-analisis/{fmeaAnalisis}', [FmeaAnalisisController::class, 'update']);

// DELETE - Menghapus analisis FMEA
Route::delete('/api/fmea-analisis/{fmeaAnalisis}', [FmeaAnalisisController::class, 'destroy']);

// POST - Membuat jadwal maintenance dari analisis
Route::post('/api/fmea-analisis/{fmeaAnalisis}/jadwal', [FmeaAnalisisController::class, 'buatJadwal']);

// GET - Filter analisis berdasarkan kategori risiko
Route::get('/api/fmea-analisis/kategori/{kategori}', [FmeaAnalisisController::class, 'getByKategori']);
// Contoh: GET /api/fmea-analisis/kategori/Sangat%20Tinggi

// GET - Mendapatkan statistik analisis
Route::get('/api/fmea-analisis/statistik/all', [FmeaAnalisisController::class, 'getStatistik']);


// ============================================
// ROUTES UNTUK JADWAL MAINTENANCE
// ============================================

// GET - Menampilkan semua jadwal maintenance
Route::get('/api/jadwal-maintenance', [JadwalMaintenanceController::class, 'index']);
// Contoh dengan filter: GET /api/jadwal-maintenance?status=Dijadwalkan&prioritas=Tinggi

// POST - Menyimpan jadwal maintenance baru
Route::post('/api/jadwal-maintenance', [JadwalMaintenanceController::class, 'store']);
// Contoh: POST /api/jadwal-maintenance dengan body JSON:
// {
//   "kode_mesin": "CP 1",
//   "komponen": "Motor Listrik",
//   "tanggal_maintenance": "2026-06-12",
//   "prioritas": "Tinggi",
//   "tindakan_maintenance": "Pemeriksaan suhu motor, pelumasan bearing",
//   "teknisi_penanggungjawab": "Teknisi 1",
//   "status": "Dijadwalkan"
// }

// GET - Menampilkan detail jadwal tertentu
Route::get('/api/jadwal-maintenance/{jadwalMaintenance}', [JadwalMaintenanceController::class, 'show']);

// PUT - Memperbarui jadwal maintenance
Route::put('/api/jadwal-maintenance/{jadwalMaintenance}', [JadwalMaintenanceController::class, 'update']);

// DELETE - Menghapus jadwal maintenance
Route::delete('/api/jadwal-maintenance/{jadwalMaintenance}', [JadwalMaintenanceController::class, 'destroy']);

// PATCH - Update status jadwal
Route::patch('/api/jadwal-maintenance/{jadwalMaintenance}/status', [JadwalMaintenanceController::class, 'updateStatus']);
// Contoh: PATCH /api/jadwal-maintenance/1/status dengan body: { "status": "Selesai" }

// GET - Jadwal yang akan datang (default 7 hari)
Route::get('/api/jadwal-maintenance/mendatang/list', [JadwalMaintenanceController::class, 'getJadwalMendatang']);
// Contoh: GET /api/jadwal-maintenance/mendatang/list?hari=14

// GET - Jadwal yang sudah lewat tapi belum selesai
Route::get('/api/jadwal-maintenance/terlambat/list', [JadwalMaintenanceController::class, 'getJadwalTerlambat']);

// GET - Statistik jadwal maintenance
Route::get('/api/jadwal-maintenance/statistik/all', [JadwalMaintenanceController::class, 'getStatistik']);

// GET - Jadwal berdasarkan mesin
Route::get('/api/jadwal-maintenance/mesin/{kodeMesin}', [JadwalMaintenanceController::class, 'getByMesin']);

// GET - Jadwal berdasarkan teknisi
Route::get('/api/jadwal-maintenance/teknisi/{teknisi}', [JadwalMaintenanceController::class, 'getByTeknisi']);


// ============================================
// CONTOH PENGGUNAAN DI FRONTEND (AXIOS / FETCH)
// ============================================

/*
// Tambah Analisis FMEA
async function tambahAnalisisFMEA(data) {
    const response = await fetch('/api/fmea-analisis', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    });
    return await response.json();
}

// Dapatkan Analisis
async function getDaftarAnalisis() {
    const response = await fetch('/api/fmea-analisis');
    return await response.json();
}

// Update Status Jadwal
async function updateStatusJadwal(jadwalId, status) {
    const response = await fetch(`/api/jadwal-maintenance/${jadwalId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    });
    return await response.json();
}

// Buat Jadwal dari Analisis
async function buatJadwalDariAnalisis(analisisId) {
    const response = await fetch(`/api/fmea-analisis/${analisisId}/jadwal`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
    return await response.json();
}
*/

// ============================================
// FILTER QUERY PARAMETERS
// ============================================

// Filter untuk GET /api/jadwal-maintenance:
// - status=Dijadwalkan|Pending|Proses|Selesai
// - prioritas=Sangat Tinggi|Tinggi|Sedang|Rendah
// - kode_mesin=CP 1
// - dari_tanggal=2026-06-01
// - sampai_tanggal=2026-06-30
// - per_page=50

// Contoh:
// GET /api/jadwal-maintenance?status=Dijadwalkan&prioritas=Tinggi&per_page=25
