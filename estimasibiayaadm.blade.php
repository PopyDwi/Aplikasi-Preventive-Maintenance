@extends('layouts.app')
@section('title', 'Estimasi Biaya Maintenance')
@php
    $activePage = 'estimasibiaya';
    $sidebarType = 'admin';
@endphp
@section('content')

<style>
    .chart-box {
        height: 320px;
        max-height: 320px;
        position: relative;
    }

    .chart-card canvas {
        width: 100% !important;
        height: 100% !important;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .input-group label {
        font-weight: 700;
        color: #334155;
    }

    .input-group input,
    .input-group select,
    .input-group textarea {
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        font-family: inherit;
        font-size: 14px;
    }

    .button-area {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 20px;
    }

    .btn-reset,
    .btn-submit {
        border: none;
        border-radius: 10px;
        padding: 12px 22px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-reset {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-submit {
        background: #3b82f6;
        color: white;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
</style>

<!-- SUMMARY CARDS -->
<div class="summary-card">
    <h2>Ringkasan Biaya Maintenance</h2>
    <div class="summary-grid">
        <div class="summary-box">
            <h3>Total Estimasi Bulan Ini</h3>
            <h1 id="totalBulanIni">Rp 0</h1>
        </div>
        <div class="summary-box">
            <h3>Total Estimasi Tahun Ini</h3>
            <h1 id="totalTahunIni">Rp 0</h1>
        </div>
        <div class="summary-box">
            <h3>Biaya Maintenance Tertinggi</h3>
            <h1 id="biayaTertinggi">Rp 0</h1>
        </div>
        <div class="summary-box">
            <h3>Total Estimasi</h3>
            <h1 id="totalPerbaikan">0</h1>
        </div>
    </div>
</div>

<!-- CHARTS -->
<div class="chart-grid" style="margin-top:18px;">
    <div class="chart-card">
        <h3>Grafik Estimasi Biaya per Bulan</h3>
        <div class="chart-box">
            <canvas id="chartMonthly"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3>Grafik Estimasi Biaya per Aset</h3>
        <div class="chart-box">
            <canvas id="chartByMachine"></canvas>
        </div>
    </div>
</div>

<!-- FORM INPUT -->
<div class="table-card" style="margin-top:18px;">
    <h2>Input Estimasi Biaya</h2>

    <form id="formEstimasi">
        <div class="form-grid">
            <div class="input-group">
                <label>Tanggal</label>
                <input type="date" id="tanggal" required>
            </div>

            <div class="input-group">
                <label>Nama Aset</label>
                <select id="kodeAset" required>
                    <option value="">Pilih Aset</option>
                    @isset($assets)
                        @foreach($assets as $aset)
                            <option value="{{ $aset->kode_aset }}">{{ $aset->nama_aset }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div class="input-group">
                <label>Komponen</label>
                <select id="kodeKomponen" required>
                    <option value="">Pilih Komponen</option>
                </select>
            </div>

            <div class="input-group">
                <label>Total Downtime (Jam)</label>
                <input type="number" id="totalDowntime" min="0" step="0.5" placeholder="Contoh: 10" required>
            </div>

            <div class="input-group">
                <label>Biaya per Jam</label>
                <input type="number" id="biayaPerJam" min="0" placeholder="Contoh: 500000" required>
            </div>

            <div class="input-group">
                <label>Biaya Perbaikan</label>
                <input type="number" id="biayaPerbaikan" min="0" placeholder="Contoh: 3000000" required>
            </div>

            <div class="input-group">
                <label>Total Estimasi (Otomatis)</label>
                <input type="number" id="totalEstimasi" readonly style="background:#d1fae5; font-weight:700;">
            </div>

            <div class="input-group" style="grid-column: 1/-1;">
                <label>Keterangan</label>
                <textarea id="keterangan" placeholder="Tambahkan catatan estimasi biaya maintenance" rows="3"></textarea>
            </div>
        </div>

        <div class="button-area">
            <button type="reset" class="btn-reset">Batal</button>
            <button type="submit" class="btn-submit">Simpan Estimasi</button>
        </div>
    </form>
</div>

<!-- FILTERS & SEARCH -->
<div class="table-card" style="margin-top:18px;">
    <h2>Riwayat Estimasi Biaya</h2>
    <div class="action-bar">
        <div class="filters">
            <select id="filterMonth" class="input-group">
                <option value="">Semua Bulan</option>
                <option value="1">Januari</option>
                <option value="2">Februari</option>
                <option value="3">Maret</option>
                <option value="4">April</option>
                <option value="5">Mei</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">Agustus</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
            </select>
            <select id="filterYear" class="input-group">
                <option value="">Semua Tahun</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026" selected>2026</option>
            </select>
            <select id="filterAset" class="input-group">
                <option value="">Semua Aset</option>
                @isset($assets)
                    @foreach($assets as $aset)
                        <option value="{{ $aset->nama_aset }}">{{ $aset->nama_aset }}</option>
                    @endforeach
                @endisset
            </select>
        </div>
        <input type="text" id="searchInput" class="search-input" placeholder="Cari nama aset / komponen...">
    </div>

    <div class="table-responsive">
        <table id="biayaTable">
            <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Aset</th>
                <th>Komponen</th>
                <th>Total Downtime</th>
                <th>Biaya per Jam</th>
                <th>Biaya Perbaikan</th>
                <th>Total Estimasi</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody id="biayaTableBody">
                <tr>
                    <td colspan="10" style="text-align:center; padding:24px; color:#64748b;">
                        Belum ada data estimasi biaya.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
let estimasiData = [];
let chartMonthly;
let chartByMachine;

function formatCurrency(num) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num || 0);
}

function hitungEstimasi() {
    const totalDowntime = parseFloat(document.getElementById('totalDowntime').value) || 0;
    const biayaPerJam = parseFloat(document.getElementById('biayaPerJam').value) || 0;
    const biayaPerbaikan = parseFloat(document.getElementById('biayaPerbaikan').value) || 0;

    const totalEstimasi = (totalDowntime * biayaPerJam) + biayaPerbaikan;
    document.getElementById('totalEstimasi').value = totalEstimasi || '';
}

['totalDowntime', 'biayaPerJam', 'biayaPerbaikan'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', hitungEstimasi);
});

// load komponen for a aset
const kodeAsetEl = document.getElementById('kodeAset');
if (kodeAsetEl) {
    kodeAsetEl.addEventListener('change', function () {
        const kodeAset = this.value;
        const komponenSelect = document.getElementById('kodeKomponen');

        komponenSelect.innerHTML = '<option value="">Pilih Komponen</option>';

        if (!kodeAset) return;

        fetch(`/api/komponen/${kodeAset}`)
            .then(response => response.json())
            .then(data => {
                const komponenList = data.data || [];
                komponenList.forEach(komponen => {
                    const option = document.createElement('option');
                    option.value = komponen.kode_komponen;
                    option.textContent = komponen.nama_komponen;
                    komponenSelect.appendChild(option);
                });
            })
            .catch(() => {
                if (typeof showToast === 'function') showToast('Gagal memuat komponen.', 'error');
            });
    });
}

function mapServerItem(item) {
    return {
        id: item.id,
        tanggal: item.tanggal ? item.tanggal.substring(0,10) : '',
        kodeAset: item.kode_aset,
        namaAset: item.aset?.nama_aset || item.kode_aset,
        kodeKomponen: item.kode_komponen,
        namaKomponen: item.komponen?.nama_komponen || item.kode_komponen || '-',
        totalDowntime: parseFloat(item.total_downtime) || 0,
        biayaPerJam: parseFloat(item.biaya_per_jam) || 0,
        biayaPerbaikan: parseFloat(item.biaya_perbaikan) || 0,
        totalEstimasi: parseFloat(item.total_estimasi) || 0,
        keterangan: item.keterangan || '',
    };
}

function loadEstimasiFromApi() {
    fetch('/api/estimasi-biaya')
        .then(r => r.json())
        .then(result => {
            if (!result.success) return;
            estimasiData = (result.data || []).map(mapServerItem);
            renderTable();
            if (!chartMonthly) initCharts(); else updateCharts();
            updateRingkasan();
        })
        .catch(() => {
            if (typeof showToast === 'function') showToast('Gagal memuat data estimasi.', 'error');
        });
}

function saveEstimasiToApi(payload) {
    return fetch('/api/estimasi-biaya', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(payload),
    }).then(r => r.json());
}

function deleteEstimasiById(id) {
    return fetch(`/api/estimasi-biaya/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken } }).then(r => r.json());
}

function updateRingkasan() {
    const now = new Date();
    const bulanIni = now.getMonth();
    const tahunIni = now.getFullYear();

    const totalBulanIni = estimasiData
        .filter(item => new Date(item.tanggal).getMonth() === bulanIni && new Date(item.tanggal).getFullYear() === tahunIni)
        .reduce((sum, item) => sum + item.totalEstimasi, 0);

    const totalTahunIni = estimasiData
        .filter(item => new Date(item.tanggal).getFullYear() === tahunIni)
        .reduce((sum, item) => sum + item.totalEstimasi, 0);

    const tertinggi = estimasiData.length ? Math.max(...estimasiData.map(item => item.totalEstimasi)) : 0;

    document.getElementById('totalBulanIni').textContent = formatCurrency(totalBulanIni);
    document.getElementById('totalTahunIni').textContent = formatCurrency(totalTahunIni);
    document.getElementById('biayaTertinggi').textContent = formatCurrency(tertinggi);
    document.getElementById('totalPerbaikan').textContent = estimasiData.length;
}

function renderTable() {
    const tbody = document.getElementById('biayaTableBody');

    if (!estimasiData.length) {
        tbody.innerHTML = `<tr><td colspan="10" style="text-align:center; padding:24px; color:#64748b;">Belum ada data estimasi biaya.</td></tr>`;
        return updateRingkasan();
    }

    tbody.innerHTML = estimasiData.map((item, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${item.tanggal}</td>
            <td>${item.namaAset}</td>
            <td>${item.namaKomponen}</td>
            <td>${item.totalDowntime} jam</td>
            <td>${formatCurrency(item.biayaPerJam)}</td>
            <td>${formatCurrency(item.biayaPerbaikan)}</td>
            <td><strong>${formatCurrency(item.totalEstimasi)}</strong></td>
            <td>${item.keterangan || '-'}</td>
            <td><a href="#" class="btn-detail" onclick="onDeleteEstimasi(${item.id}); return false;">Hapus</a></td>
        </tr>
    `).join('');

    updateRingkasan();
    applyFilters();
}

function onDeleteEstimasi(id) {
    if (!confirm('Hapus data estimasi ini?')) return;
    deleteEstimasiById(id).then(res => {
        if (res.success) {
            if (typeof showToast === 'function') showToast(res.message || 'Estimasi biaya berhasil dihapus.', 'success');
            loadEstimasiFromApi();
        } else {
            if (typeof showToast === 'function') showToast(res.message || 'Gagal menghapus data.', 'error');
        }
    }).catch(() => { if (typeof showToast === 'function') showToast('Gagal menghapus data.', 'error'); });
}

function applyFilters() {
    const q = document.getElementById('searchInput').value.trim().toLowerCase();
    const month = document.getElementById('filterMonth').value;
    const year = document.getElementById('filterYear').value;
    const aset = document.getElementById('filterAset').value.toLowerCase();
    const tbody = document.querySelector('#biayaTable tbody');

    Array.from(tbody.rows).forEach(row => {
        if (row.cells.length < 10) return;

        const text = row.textContent.toLowerCase();
        const date = row.cells[1].textContent.trim();
        const namaAset = row.cells[2].textContent.trim().toLowerCase();

        let show = true;
        if (q && !text.includes(q)) show = false;
        if (month && date) { const m = new Date(date).getMonth() + 1; if (String(m) !== month) show = false; }
        if (year && date) { const y = new Date(date).getFullYear(); if (String(y) !== year) show = false; }
        if (aset && namaAset !== aset) show = false;

        row.style.display = show ? '' : 'none';
    });
}

document.getElementById('searchInput').addEventListener('input', applyFilters);
['filterMonth', 'filterYear', 'filterAset'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', applyFilters);
});

function getMonthlyChartData() {
    const labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const values = Array(12).fill(0);

    estimasiData.forEach(item => {
        const date = new Date(item.tanggal);
        values[date.getMonth()] += item.totalEstimasi;
    });

    return { labels, values: values.map(v => v / 1000000) };
}

function getMachineChartData() {
    const grouped = {};
    estimasiData.forEach(item => { grouped[item.namaAset] = (grouped[item.namaAset] || 0) + item.totalEstimasi; });
    return { labels: Object.keys(grouped), values: Object.values(grouped).map(v => v / 1000000) };
}

function initCharts() {
    const monthly = getMonthlyChartData();
    const machine = getMachineChartData();

    const ctx = document.getElementById('chartMonthly');
    if (ctx) {
        chartMonthly = new Chart(ctx.getContext('2d'), { type: 'bar', data: { labels: monthly.labels, datasets: [{ label: 'Biaya (Juta Rp)', data: monthly.values, backgroundColor: 'rgba(59,130,246,0.8)' }] }, options: { responsive: true, maintainAspectRatio: false } });
    }

    const ctx2 = document.getElementById('chartByMachine');
    if (ctx2) {
        chartByMachine = new Chart(ctx2.getContext('2d'), { type: 'doughnut', data: { labels: machine.labels.length ? machine.labels : ['Belum ada data'], datasets: [{ data: machine.values.length ? machine.values : [1], backgroundColor: ['#3b82f6','#60a5fa','#93c5fd','#bfdbfe','#1d4ed8'] }] }, options: { responsive: true, maintainAspectRatio: false } });
    }
}

function updateCharts() {
    if (!chartMonthly || !chartByMachine) return;
    const monthly = getMonthlyChartData();
    const machine = getMachineChartData();
    chartMonthly.data.datasets[0].data = monthly.values; chartMonthly.update();
    chartByMachine.data.labels = machine.labels.length ? machine.labels : ['Belum ada data'];
    chartByMachine.data.datasets[0].data = machine.values.length ? machine.values : [1]; chartByMachine.update();
}

document.getElementById('formEstimasi').addEventListener('submit', function(e) {
    e.preventDefault();

    const payload = {
        tanggal: document.getElementById('tanggal').value,
        kode_aset: document.getElementById('kodeAset').value,
        kode_komponen: document.getElementById('kodeKomponen').value || null,
        total_downtime: parseFloat(document.getElementById('totalDowntime').value) || 0,
        biaya_per_jam: parseFloat(document.getElementById('biayaPerJam').value) || 0,
        biaya_perbaikan: parseFloat(document.getElementById('biayaPerbaikan').value) || 0,
        keterangan: document.getElementById('keterangan').value || null,
    };

    if (!payload.tanggal || !payload.kode_aset || !payload.total_downtime || !payload.biaya_per_jam) {
        if (typeof showToast === 'function') showToast('Lengkapi data estimasi terlebih dahulu.', 'warning');
        return;
    }

    saveEstimasiToApi(payload).then(res => {
        if (res.success) {
            if (typeof showToast === 'function') showToast(res.message || 'Estimasi biaya berhasil disimpan.', 'success');
            loadEstimasiFromApi();
            document.getElementById('formEstimasi').reset();
            document.getElementById('kodeKomponen').innerHTML = '<option value="">Pilih Komponen</option>';
        } else {
            if (typeof showToast === 'function') showToast(res.message || 'Gagal menyimpan estimasi.', 'error');
        }
    }).catch(() => { if (typeof showToast === 'function') showToast('Gagal menyimpan estimasi.', 'error'); });
});

document.addEventListener('DOMContentLoaded', function () {
    loadEstimasiFromApi();
    initCharts();
});
</script>
@endpush

@endsection
