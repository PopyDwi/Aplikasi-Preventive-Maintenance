@extends('layouts.app')
@section('title', 'Jadwal Maintenance')
@php
    $activePage = 'jadwalmaintenance';
    $sidebarType = 'admin';
@endphp
@section('content')

<style>
    .filter-card, .table-card, .info-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        margin-bottom: 24px;
    }

    .filter-card h2, .table-card h2 {
        font-size: 18px;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 20px;
    }

    .filter-grid {
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        margin-bottom: 20px;
    }

    .input-group {
        display: flex;
        flex-direction: column;
    }

    .input-group label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .input-group select,
    .input-group input {
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background: #f8fafc;
    }

    .filter-buttons {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    .btn-filter, .btn-reset-filter {
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .btn-filter {
        background: #3b82f6;
        color: white;
    }

    .btn-filter:hover {
        background: #2563eb;
    }

    .btn-reset-filter {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-reset-filter:hover {
        background: #cbd5e1;
    }

    /* Info Card */
    .info-card {
        background: #f0f9ff;
        border: 1px solid #bfdbfe;
        border-left: 4px solid #3b82f6;
        margin-bottom: 24px;
    }

    .info-card h3 {
        color: #1e40af;
        margin: 0 0 12px 0;
        font-size: 14px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 16px;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .info-label {
        font-weight: 600;
        color: #334155;
    }

    .info-value {
        color: #1e3a8a;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th {
        background: #1d4ed8;
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: 700;
        font-size: 13px;
    }

    table td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
    }

    table tbody tr:hover {
        background: #f8fafc;
    }

    .priority, .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .priority.sangat-tinggi,
    .status.sangat-tinggi {
        background: #fee2e2;
        color: #991b1b;
    }

    .priority.tinggi,
    .status.tinggi {
        background: #fed7aa;
        color: #92400e;
    }

    .priority.sedang,
    .status.sedang {
        background: #fef3c7;
        color: #92400e;
    }

    .priority.rendah,
    .status.rendah {
        background: #dcfce7;
        color: #166534;
    }

    .status.dijadwalkan {
        background: #bfdbfe;
        color: #1e40af;
    }

    .status.diproses {
        background: #fed7aa;
        color: #92400e;
    }

    .status.selesai {
        background: #dcfce7;
        color: #166534;
    }

    .status.dibatalkan {
        background: #e5e7eb;
        color: #374151;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 8px 16px;
        font-size: 12px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        background: #2563eb;
    }

    .btn-action.danger {
        background: #ef4444;
    }

    .btn-action.danger:hover {
        background: #dc2626;
    }

    .btn-action:disabled {
        background: #cbd5e1;
        color: #64748b;
        cursor: not-allowed;
    }

    .no-data {
        text-align: center;
        padding: 32px;
        color: #64748b;
    }

    .table-scroll {
        overflow-x: auto;
    }

    /* Modal Styles */
    .modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,.45);
    justify-content:center;
    align-items:center;
    z-index:9999;
}

    .modal-content {
        background-color: white;
        margin: 50px auto;
        padding: 24px;
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s ease;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 16px;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 18px;
        color: #1e3a8a;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #64748b;
    }

    .close-btn:hover {
        color: #1e3a8a;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background: #f8fafc;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-cancel, .btn-save {
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .btn-cancel {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-cancel:hover {
        background: #cbd5e1;
    }

    .btn-save {
        background: #3b82f6;
        color: white;
    }

    .btn-save:hover {
        background: #2563eb;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .badge-info {
        display: inline-block;
        background: #e0e7ff;
        color: #3730a3;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 8px;
    }

    .btn-whatsapp {
        background: #22c55e;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        justify-content: center;
    }

    .btn-whatsapp:hover {
        background: #16a34a;
    }

    .btn-whatsapp:disabled {
        background: #cbd5e1;
        color: #64748b;
        cursor: not-allowed;
    }

    .whatsapp-section {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 2px solid #e2e8f0;
    }

    @media (max-width: 768px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
        }

        .modal-content {
            width: 95%;
            margin: 20px auto;
        }

        .info-row {
            grid-template-columns: 100px 1fr;
        }
    }
</style>

<!-- Filter Card -->
<div class="filter-card">
    <h2>Filter Jadwal Maintenance</h2>
    <div class="filter-grid">
        <div class="input-group">
            <label>Status</label>
            <select id="filterStatus">
                <option value="">Semua Status</option>
                <option value="Dijadwalkan">Dijadwalkan</option>
                <option value="Selesai">Selesai</option>
            </select>
        </div>

        <div class="input-group">
            <label>Prioritas</label>
            <select id="filterPrioritas">
                <option value="">Semua Prioritas</option>
                <option value="Sangat Tinggi">Sangat Tinggi</option>
                <option value="Tinggi">Tinggi</option>
                <option value="Sedang">Sedang</option>
                <option value="Rendah">Rendah</option>
            </select>
        </div>

        <div class="input-group">
            <label>Dari Tanggal</label>
            <input type="date" id="filterDariTanggal">
        </div>

        <div class="input-group">
            <label>Sampai Tanggal</label>
            <input type="date" id="filterSampaiTanggal">
        </div>
    </div>
    
    <div class="filter-buttons">
        <button type="button" class="btn-filter" onclick="applyFilter()">Terapkan Filter</button>
        <button type="button" class="btn-reset-filter" onclick="resetFilter()">Reset Filter</button>
    </div>
</div>

<!-- TABLE -->
<div class="table-card">
    <h2>Daftar Jadwal Maintenance</h2>
    
    <div class="table-responsive">
        <table id="jadwalTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Aset</th>
                    <th>Komponen</th>
                    <th>Tanggal Maintenance</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Penanggung Jawab</th>
                    <th>WhatsApp</th>
                    <th>Tindakan Maintenance</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="jadwalTableBody">
                <tr>
                    <td colspan="8" class="no-data">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Jadwal Maintenance</h2>
            <button type="button" class="close-btn" onclick="closeEditModal()">&times;</button>
        </div>
        
        <div id="editDetailInfo" style="margin-bottom: 20px;"></div>

        <form id="editForm">
            <div class="form-group">
                <label>Status</label>
                <select id="editStatus" required>
                    <option value="Dijadwalkan">Dijadwalkan</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div class="form-group">
                <label>Catatan Tambahan</label>
                <textarea id="editCatatan" placeholder="Catatan atau informasi tambahan (opsional)"></textarea>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
            <button type="button" class="btn-save" onclick="saveEdit()">Simpan Perubahan</button>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Detail Jadwal Maintenance</h2>
            <button type="button" class="close-btn" onclick="closeDetailModal()">&times;</button>
        </div>
        
        <div id="detailContent"></div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeDetailModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
    let allJadwal = [];
    let editingId = null;
    let detailJadwal = null;

    // Fungsi untuk memformat tanggal ke format Indonesia
    function formatTanggal(date) {
        if (!date) return '-';

        // Jika sudah objek Date
        if (date instanceof Date) {
            return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
        }

        // Jika string, bisa berbentuk 'YYYY-MM-DD' atau ISO lengkap
        if (typeof date === 'string') {
            // Jika string sederhana seperti 'YYYY-MM-DD', tambahkan time agar Date parsing konsisten
            const needsAppend = !date.includes('T') && !date.includes(' ');
            const parsed = new Date(needsAppend ? (date + 'T00:00:00') : date);
            if (isNaN(parsed)) return '-';
            return parsed.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
        }

        // Fallback: coba konversi langsung
        const d = new Date(date);
        if (isNaN(d)) return '-';
        return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    // Fungsi untuk mengubah kategori risiko menjadi class CSS
    function getPrioritasClass(prioritas) {
        const mapping = {
            'Sangat Tinggi': 'sangat-tinggi',
            'Tinggi': 'tinggi',
            'Sedang': 'sedang',
            'Rendah': 'rendah'
        };
        return mapping[prioritas] || 'rendah';
    }

    // Fungsi untuk mengubah status menjadi class CSS
    function getStatusClass(status) {
        const mapping = {
            'Dijadwalkan': 'dijadwalkan',
            'Diproses': 'diproses',
            'Selesai': 'selesai',
            'Dibatalkan': 'dibatalkan'
        };
        return mapping[status] || 'dijadwalkan';
    }

    // Load jadwal dari API
    function loadJadwal() {
        fetch('/api/jadwal-maintenance')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allJadwal = data.data.data || data.data || [];
                    updateJadwalTable(allJadwal);
                } else {
                    showToast('Gagal memuat data jadwal', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error memuat data', 'error');
            });
    }

    // Update tabel jadwal
    function updateJadwalTable(jadwalList) {
        const tbody = document.getElementById('jadwalTableBody');
        
        if (jadwalList.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="no-data">Belum ada jadwal maintenance.</td></tr>';
            return;
        }

        tbody.innerHTML = jadwalList.map((item, index) => {
            
            const prioritasClass = getPrioritasClass(item.prioritas);
            const statusValue = item.status_jadwal || item.status || 'Dijadwalkan';
            const statusClass = getStatusClass(statusValue);
            const statusJadwal = statusValue;
            const namaAset = item.nama_aset || item.kode_aset || '-';
            const namaKomponen = item.nama_komponen || item.kode_komponen || '-';
            const fromRcm = item.analisis_rcm_id ? '<span class="badge-info">Dari RCM</span>' : '';
            
            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>${namaAset}</td>
                    <td>${namaKomponen}</td>
                    <td>${formatTanggal(item.tanggal_maintenance)}</td>
                    <td><span class="priority ${prioritasClass}">${item.prioritas}</span></td>
                    <td><span class="status ${statusClass}">${statusJadwal}</span></td>
                    <td>${item.penanggungjawab || '-'}</td>
                    <td>${item.nomor_whatsapp || '-'}</td>
                    <td title="${item.tindakan_maintenance || ''}" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${item.tindakan_maintenance ? item.tindakan_maintenance.substring(0, 50) + '...' : '-'}
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="btn-action" onclick="viewDetail(${item.id})">Lihat</button>
                            <button type="button" class="btn-action" onclick="openEditModal(${item.id})">Edit</button>
                            ${item.fmea_analisis_id || item.analisis_rcm_id ? '<button type="button" class="btn-action danger" onclick="hapusJadwal('+item.id+')">Hapus</button>' : `<button type="button" class="btn-action danger" onclick="hapusJadwal(${item.id})">Hapus</button>`}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Apply filter
    function applyFilter() {
        const status = document.getElementById('filterStatus').value;
        const prioritas = document.getElementById('filterPrioritas').value;
        const dariTanggal = document.getElementById('filterDariTanggal').value;
        const sampaiTanggal = document.getElementById('filterSampaiTanggal').value;

        let filtered = allJadwal;

        if (status) {
            filtered = filtered.filter(j => (j.status_jadwal || j.status) === status);
        }

        if (prioritas) {
            filtered = filtered.filter(j => j.prioritas === prioritas);
        }

        if (dariTanggal) {
            filtered = filtered.filter(j => new Date(j.tanggal_maintenance) >= new Date(dariTanggal));
        }

        if (sampaiTanggal) {
            filtered = filtered.filter(j => new Date(j.tanggal_maintenance) <= new Date(sampaiTanggal));
        }

        updateJadwalTable(filtered);
    }

    // Reset filter
    function resetFilter() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterPrioritas').value = '';
        document.getElementById('filterDariTanggal').value = '';
        document.getElementById('filterSampaiTanggal').value = '';
        updateJadwalTable(allJadwal);
    }

    // Open edit modal
    function openEditModal(id) {
        const jadwal = allJadwal.find(j => j.id === id);
        if (!jadwal) return;

        editingId = id;

        // Set detail info
        const namaAset = jadwal.nama_aset || jadwal.kode_aset || '-';
        const namaKomponen = jadwal.nama_komponen || jadwal.kode_komponen || '-';
        document.getElementById('editDetailInfo').innerHTML = `
            <div class="info-card">
                <h3>ℹ Data Jadwal</h3>
                <div class="info-row">
                    <div class="info-label">Aset:</div>
                    <div class="info-value">${namaAset}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Komponen:</div>
                    <div class="info-value">${namaKomponen}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal:</div>
                    <div class="info-value">${formatTanggal(jadwal.tanggal_maintenance)}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Prioritas:</div>
                    <div class="info-value"><span class="priority ${getPrioritasClass(jadwal.prioritas)}">${jadwal.prioritas}</span></div>
                </div>
            </div>
        `;

        document.getElementById('editStatus').value = jadwal.status_jadwal || jadwal.status || 'Dijadwalkan';
        document.getElementById('editCatatan').value = jadwal.catatan_tambahan || '';

        document.getElementById('editModal').style.display = 'block';
    }

    // Close edit modal
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
        editingId = null;
    }

    // Save edit
    function saveEdit() {
        if (!editingId) return;

        const status = document.getElementById('editStatus').value;
        const catatan = document.getElementById('editCatatan').value;

        const data = {
            status: status,
            catatan_tambahan: catatan
        };

        fetch(`/api/jadwal-maintenance/${editingId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Jadwal berhasil diperbarui', 'success');
                closeEditModal();
                loadJadwal();
            } else {
                showToast(data.message || 'Gagal memperbarui jadwal', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error saat menyimpan', 'error');
        });
    }

    // ==========================
// DETAIL JADWAL MAINTENANCE
// ==========================
function viewDetail(id) {

    const jadwal = allJadwal.find(item => item.id == id);

    if (!jadwal) {
        showToast('Data jadwal tidak ditemukan.', 'error');
        return;
    }

    detailJadwal = jadwal;

    const namaAset = jadwal.nama_aset || jadwal.aset?.nama_aset || jadwal.kode_aset || '-';
    const namaKomponen = jadwal.nama_komponen || jadwal.komponen?.nama_komponen || jadwal.kode_komponen || '-';

    const statusValue = jadwal.status_jadwal || 'Dijadwalkan';

    const prioritasClass = getPrioritasClass(jadwal.prioritas);
    const statusClass = getStatusClass(statusValue);

    document.getElementById("detailContent").innerHTML = `
        <div class="info-card">

            <h3>📋 Detail Jadwal Maintenance</h3>

            <div class="info-row">
                <div class="info-label">Nama Aset</div>
                <div class="info-value">${namaAset}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Nama Komponen</div>
                <div class="info-value">${namaKomponen}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Mode Kegagalan</div>
                <div class="info-value">${jadwal.mode_kegagalan ?? '-'}</div>
            </div>

            <div class="info-row">
                <div class="info-label">RPN</div>
                <div class="info-value">${jadwal.rpn ?? '-'}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Kategori Risiko</div>
                <div class="info-value">${jadwal.kategori_risiko ?? '-'}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Prioritas</div>
                <div class="info-value">
                    <span class="priority ${prioritasClass}">
                        ${jadwal.prioritas}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Tanggal Maintenance</div>
                <div class="info-value">
                    ${formatTanggal(jadwal.tanggal_maintenance)}
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status ${statusClass}">
                        ${statusValue}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Penanggung Jawab</div>
                <div class="info-value">
                    ${jadwal.penanggungjawab ?? '-'}
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Nomor WhatsApp</div>
                <div class="info-value">
                    ${jadwal.nomor_whatsapp ?? '-'}
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Tindakan Maintenance</div>
                <div class="info-value">
                    ${jadwal.tindakan_maintenance ?? '-'}
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Catatan</div>
                <div class="info-value">
                    ${jadwal.catatan_tambahan ?? '-'}
                </div>
            </div>

            <div class="whatsapp-section">

                <button
                    type="button"
                    class="btn-whatsapp"
                    onclick="openWhatsApp()"
                    ${!jadwal.nomor_whatsapp ? 'disabled' : ''}
                >

                    📱 Kirim WhatsApp

                </button>

            </div>

        </div>
    `;

    document.getElementById("detailModal").style.display = "flex";

}

   function openWhatsApp(){

    if(!detailJadwal){
        showToast("Data belum tersedia","error");
        return;
    }

    if(!detailJadwal.nomor_whatsapp){
        showToast("Nomor WhatsApp belum tersedia","error");
        return;
    }

    let nomor = detailJadwal.nomor_whatsapp.replace(/\D/g,'');

    if(nomor.startsWith("0")){
        nomor = "62"+nomor.substring(1);
    }

    const pesan =
`Halo ${detailJadwal.penanggungjawab},

Ini adalah pengingat jadwal Preventive Maintenance.

Aset :
${detailJadwal.nama_aset}

Komponen :
${detailJadwal.nama_komponen}

Tanggal :
${formatTanggal(detailJadwal.tanggal_maintenance)}

Prioritas :
${detailJadwal.prioritas}

Mohon segera melaksanakan maintenance sesuai jadwal.

Terima kasih.`;

    window.open(
        "https://wa.me/"+nomor+"?text="+encodeURIComponent(pesan),
        "_blank"
    );

}

    // Close detail modal
    function closeDetailModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    // Hapus jadwal
    function hapusJadwal(id) {
        if (confirm('Yakin ingin menghapus jadwal ini?')) {
            fetch(`/api/jadwal-maintenance/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Jadwal berhasil dihapus', 'success');
                    loadJadwal();
                } else {
                    showToast(data.message || 'Gagal menghapus jadwal', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error saat menghapus', 'error');
            });
        }
    }

    // Close modals saat klik di luar
    window.onclick = function(event) {
        const editModal = document.getElementById('editModal');
        const detailModal = document.getElementById('detailModal');
        
        if (event.target === editModal) {
            editModal.style.display = 'none';
        }
        if (event.target === detailModal) {
            detailModal.style.display = 'none';
        }
    }

    // Load jadwal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        loadJadwal();
    });

    // Refresh setiap 30 detik
    setInterval(loadJadwal, 30000);
</script>
@endsection
