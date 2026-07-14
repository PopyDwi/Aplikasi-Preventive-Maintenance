@extends('layouts.app')
@section('title', 'Data Kerusakan')
@php
    $activePage = 'datakerusakan';
    $sidebarType = 'admin';
@endphp
@section('content')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        border: 1px solid #e2e8f0;
    }

    .summary-card h3 {
        margin: 0 0 10px;
        font-size: 14px;
        color: #334155;
        font-weight: 700;
    }

    .summary-card p {
        margin: 0;
        font-size: 28px;
        color: #0f172a;
        font-weight: 700;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .filter-card {
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }

    .filter-card label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1f2937;
    }

    .filter-card select,
    .filter-card input {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 14px;
        background: #f8fafc;
        color: #0f172a;
    }

    .filter-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 16px;
    }

    .filter-actions button {
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        cursor: pointer;
        font-weight: 700;
    }

    .filter-actions .btn-primary {
        background: #2563eb;
        color: white;
    }

    .filter-actions .btn-secondary {
        background: #e2e8f0;
        color: #334155;
    }

    .table-card {
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        border: 1px solid #e2e8f0;
    }

    .table-card table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
    }

    .table-card th,
    .table-card td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
    }

    .table-card th {
        color: #334155;
        font-weight: 700;
        background: #f8fafc;
    }

    .table-card tbody tr:hover {
        background: #f1f5f9;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .status-belum {
        background: #fef3c7;
        color: #92400e;
    }

    .status-proses {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-selesai {
        background: #dcfce7;
        color: #166534;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-buttons button {
        border: none;
        border-radius: 10px;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
    }

    .btn-edit {
        background: #2563eb;
        color: white;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-content {
        width: 100%;
        max-width: 680px;
        background: white;
        border-radius: 20px;
        padding: 24px;
        position: relative;
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.18);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 20px;
        color: #0f172a;
    }

    .close-btn {
        border: none;
        background: transparent;
        font-size: 24px;
        cursor: pointer;
        color: #64748b;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        color: #334155;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 14px;
        background: #f8fafc;
        color: #0f172a;
    }

    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 20px;
    }

    .modal-footer button {
        border: none;
        border-radius: 12px;
        padding: 12px 18px;
        cursor: pointer;
        font-weight: 700;
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #334155;
    }

    .btn-primary {
        background: #2563eb;
        color: white;
    }

    @media (max-width: 1024px) {
        .summary-grid,
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 720px) {
        .summary-grid,
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .table-card table {
            min-width: 100%;
        }
    }
</style>

<div class="content-card">
    <div class="topbar">
        <h1>Data Kerusakan Mesin</h1>
        <!-- <p>Halaman ini menampilkan semua laporan kerusakan teknisi, downtime, estimasi biaya, dan status penanganan.</p> -->
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <h3>Total Kerusakan</h3>
            <p id="summaryTotal">0</p>
        </div>
        <div class="summary-card">
            <h3>Belum Ditangani</h3>
            <p id="summaryBelum">0</p>
        </div>
        <div class="summary-card">
            <h3>Dalam Proses</h3>
            <p id="summaryProses">0</p>
        </div>
        <div class="summary-card">
            <h3>Selesai</h3>
            <p id="summarySelesai">0</p>
        </div>
    </div>

    <!-- Damage Trend Chart -->
    <div class="table-card" style="margin-bottom: 24px;">
        <h2>Tren Kerusakan per Bulan</h2>
        <div style="height: 320px; position: relative;">
            <canvas id="damageTrendChart"></canvas>
        </div>
    </div>

    <div class="filter-card">
        <div class="filter-grid">
            <div>
                <label>Status</label>
                <select id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="Belum Ditangani">Belum Ditangani</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div>
                <label>Nama Aset</label>
                <select id="filterAset">
                    <option value="">Semua Aset</option>
                </select>
            </div>

            <div>
                <label>Dari Tanggal</label>
                <input type="date" id="filterDariTanggal">
            </div>

            <div>
                <label>Sampai Tanggal</label>
                <input type="date" id="filterSampaiTanggal">
            </div>
        </div>

        <div class="filter-actions">
            <button type="button" class="btn-secondary" onclick="resetFilter()">Reset Filter</button>
            <button type="button" class="btn-primary" onclick="applyFilter()">Terapkan Filter</button>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Kerusakan</th>
                        <th>Nama Aset</th>
                        <th>Nama Komponen</th>
                        <th>Jenis / Deskripsi</th>
                        <th>Downtime (Jam)</th>
                        <th>Teknisi Pelapor</th>
                        <th>Status</th>
                        <th>Estimasi Biaya</th>
                        <th>Dokumentasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="kerusakanTableBody">
                    <tr>
                        <td colspan="10" class="no-data">Memuat data kerusakan...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Kerusakan</h2>
            <button type="button" class="close-btn" onclick="closeEditModal()">&times;</button>
        </div>

        <form id="editForm">
            <div class="form-group">
                <label>Status</label>
                <select id="editStatus">
                    <option value="Belum Ditangani">Belum Ditangani</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div class="form-group">
                <label>Downtime (Jam)</label>
                <input type="number" id="editDowntime" step="0.1" min="0">
            </div>

            <div class="form-group">
                <label>Estimasi Biaya</label>
                <input type="number" id="editEstimasiBiaya" step="0.01" min="0">
            </div>

            <div class="form-group">
                <label>Catatan Teknisi</label>
                <textarea id="editCatatan"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="button" class="btn-primary" onclick="saveEdit()">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    let allKerusakan = [];
    let editingId = null;

    function formatTanggal(date) {
        if (!date) return '-';
        const dt = new Date(date);
        return dt.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function formatRupiah(value) {
        if (value === null || value === undefined || isNaN(value)) return '-';
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
    }

    function getStatusClass(status) {
        if (status === 'Belum Ditangani') return 'status-belum';
        if (status === 'Diproses') return 'status-proses';
        if (status === 'Selesai') return 'status-selesai';
        return 'status-belum';
    }

    function loadKerusakan() {
        fetch('/api/kerusakan')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allKerusakan = data.data || [];
                    populateAssetFilter(allKerusakan);
                    renderSummary(data.summary);
                    updateKerusakanTable(allKerusakan);
                } else {
                    showToast('Gagal memuat data kerusakan.', 'error');
                }
            })
            .catch(() => {
                showToast('Gagal memuat data kerusakan.', 'error');
            });
    }

    function populateAssetFilter(records) {
        const asetSelect = document.getElementById('filterAset');
        const asetList = [...new Set(records.map(r => r.nama_aset || r.kode_aset))].sort();
        asetSelect.innerHTML = '<option value="">Semua Aset</option>' + asetList.map(aset => `<option value="${aset}">${aset}</option>`).join('');
    }

    function renderSummary(summary) {
        document.getElementById('summaryTotal').textContent = summary.total;
        document.getElementById('summaryBelum').textContent = summary.belum;
        document.getElementById('summaryProses').textContent = summary.proses;
        document.getElementById('summarySelesai').textContent = summary.selesai;
    }

    function updateKerusakanTable(records) {
        const tbody = document.getElementById('kerusakanTableBody');

        if (!records.length) {
            tbody.innerHTML = '<tr><td colspan="10" class="no-data">Belum ada data kerusakan.</td></tr>';
            return;
        }

        tbody.innerHTML = records.map((item, index) => {
            const statusClass = getStatusClass(item.status);
            const namaAset = item.nama_aset || item.kode_aset || '-';
            const namaKomponen = item.nama_komponen || item.kode_komponen || '-';
            const deskripsi = item.deskripsi_kerusakan ? item.deskripsi_kerusakan : item.jenis_kerusakan;

            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>${formatTanggal(item.tanggal_kerusakan)}</td>
                    <td>${namaAset}</td>
                    <td>${namaKomponen}</td>
                    <td style="max-width:250px; white-space: nowrap; overflow:hidden; text-overflow:ellipsis;" title="${deskripsi}">${deskripsi}</td>
                    <td>${item.downtime_jam ?? 0}</td>
                    <td>${item.teknisi_pelapor || '-'}</td>
                    <td><span class="status-pill ${statusClass}">${item.status}</span></td>
                    <td>${formatRupiah(item.estimasi_biaya)}</td>
                    <td>${item.foto_kerusakan ? `<a class="btn-edit" target="_blank" href="${item.foto_kerusakan.startsWith('/') ? item.foto_kerusakan : '/storage/' + item.foto_kerusakan}">Lihat Dokumentasi</a>` : '-'}</td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="btn-edit" onclick="openEditModal(${item.id})">Edit</button>
                            <button type="button" class="btn-delete" onclick="hapusKerusakan(${item.id})">Hapus</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function applyFilter() {
        const status = document.getElementById('filterStatus').value;
        const aset = document.getElementById('filterAset').value;
        const dariTanggal = document.getElementById('filterDariTanggal').value;
        const sampaiTanggal = document.getElementById('filterSampaiTanggal').value;

        let filtered = [...allKerusakan];

        if (status) {
            filtered = filtered.filter(item => item.status === status);
        }

        if (aset) {
            filtered = filtered.filter(item => (item.nama_aset || item.kode_aset) === aset);
        }

        if (dariTanggal) {
            filtered = filtered.filter(item => new Date(item.tanggal_kerusakan) >= new Date(dariTanggal));
        }

        if (sampaiTanggal) {
            filtered = filtered.filter(item => new Date(item.tanggal_kerusakan) <= new Date(sampaiTanggal));
        }

        updateKerusakanTable(filtered);
    }

    function resetFilter() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterAset').value = '';
        document.getElementById('filterDariTanggal').value = '';
        document.getElementById('filterSampaiTanggal').value = '';
        updateKerusakanTable(allKerusakan);
    }

    function openEditModal(id) {
        const item = allKerusakan.find(entry => entry.id === id);
        if (!item) return;

        editingId = id;
        document.getElementById('editStatus').value = item.status;
        document.getElementById('editDowntime').value = item.downtime_jam ?? 0;
        document.getElementById('editEstimasiBiaya').value = item.estimasi_biaya ?? 0;
        document.getElementById('editCatatan').value = item.catatan_teknisi || '';
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        editingId = null;
        document.getElementById('editModal').style.display = 'none';
    }

    function saveEdit() {
        if (!editingId) return;

        const data = {
            status: document.getElementById('editStatus').value,
            downtime_jam: document.getElementById('editDowntime').value,
            estimasi_biaya: document.getElementById('editEstimasiBiaya').value,
            catatan_teknisi: document.getElementById('editCatatan').value,
        };

        fetch(`/api/kerusakan/${editingId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast(result.message || 'Data kerusakan berhasil diperbarui.', 'success');
                closeEditModal();
                loadKerusakan();
            } else {
                showToast(result.message || 'Gagal memperbarui data kerusakan.', 'error');
            }
        })
        .catch(() => {
            showToast('Gagal memperbarui data kerusakan.', 'error');
        });
    }

    function hapusKerusakan(id) {
        if (!confirm('Yakin ingin menghapus data kerusakan ini?')) {
            return;
        }

        fetch(`/api/kerusakan/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast(result.message || 'Data kerusakan berhasil dihapus.', 'success');
                loadKerusakan();
            } else {
                showToast(result.message || 'Gagal menghapus data kerusakan.', 'error');
            }
        })
        .catch(() => {
            showToast('Gagal menghapus data kerusakan.', 'error');
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadKerusakan();
        initDamageTrendChart();
    });

    // Initialize Damage Trend Chart
    let damageTrendChartInstance = null;

    function initDamageTrendChart() {
        const ctx = document.getElementById('damageTrendChart');
        if (!ctx) return;

        // Generate last 12 months
        const months = [];
        const monthCounts = {};
        const now = new Date();
        
        for (let i = 11; i >= 0; i--) {
            const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            const monthKey = d.toLocaleString('id-ID', { month: 'short', year: 'numeric' });
            months.push(monthKey);
            monthCounts[monthKey] = 0;
        }

        // Count kerusakan by month from fetched data
        if (allKerusakan && allKerusakan.length > 0) {
            allKerusakan.forEach(item => {
                if (item.tanggal_kerusakan) {
                    const d = new Date(item.tanggal_kerusakan);
                    const monthKey = d.toLocaleString('id-ID', { month: 'short', year: 'numeric' });
                    if (monthCounts.hasOwnProperty(monthKey)) {
                        monthCounts[monthKey]++;
                    }
                }
            });
        }

        const counts = months.map(m => monthCounts[m] || 0);

        if (damageTrendChartInstance) {
            damageTrendChartInstance.destroy();
        }

        damageTrendChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Jumlah Kerusakan',
                    data: counts,
                    backgroundColor: '#ef4444',
                    borderColor: '#dc2626',
                    borderWidth: 2,
                    borderRadius: 8,
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 13, weight: '600' },
                            color: '#334155',
                            usePointStyle: true,
                            padding: 16,
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        grid: { color: '#e2e8f0', drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e2e8f0', drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 12 } }
                    }
                }
            }
        });
    }
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
@endsection
