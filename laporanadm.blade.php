@extends('layouts.app')

@section('title', 'Laporan Maintenance')

@section('content')

<style>
    .preview-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.7);
        overflow: auto;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .preview-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .preview-content {
        background: #ffffff;
        padding: 40px;
        border-radius: 24px;
        width: 90%;
        max-width: 1100px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.2);
        animation: slideUp 0.3s ease;
    }

    #previewChartContainer {
        display: none;
        margin: 24px 0;
        padding: 24px;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }

    .preview-chart-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        max-height: 400px;
    }

    .preview-chart-box {
        position: relative;
        height: 300px;
        background: #ffffff;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #e2e8f0;
    }

    #previewChart,
    #previewDistributionChart {
        max-width: 100%;
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 16px;
    }

    .preview-header h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }
    .preview-body {
        padding: 0;
    }

    .chart-section {
        display: none;
        margin: 20px 0;
        padding: 20px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .chart-section.show {
        display: block;
    }

    .chart-title {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 16px;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .chart-wrapper {
        position: relative;
        height: 280px;
        background: #ffffff;
        border-radius: 8px;
        padding: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
    }

    canvas.report-chart {
        max-width: 100%;
        max-height: 260px;
    }

    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }
    }
    .preview-close {
        background: none;
        border: none;
        font-size: 32px;
        cursor: pointer;
        color: #64748b;
        padding: 0;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .preview-close:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .preview-body {
        margin-bottom: 24px;
    }

    .preview-body table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }

    .preview-body table thead {
        background: #1d4ed8;
        color: white;
    }

    .preview-body table th {
        padding: 14px;
        text-align: left;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.1em;
    }

    .preview-body table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
        color: #334155;
    }

    .preview-body table tbody tr:hover {
        background: #f1f5f9;
    }

    .preview-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        border-top: 2px solid #e2e8f0;
        padding-top: 20px;
    }

    .letterhead-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding-bottom: 20px;
        border-bottom: 3px double #1e3a8a;
        margin-bottom: 20px;
    }

    .letterhead-section img {
        width: 100px;
        height: auto;
        object-fit: contain;
    }

    .letterhead-text {
        flex: 1;
        text-align: center;
        color: #1e3a8a;
    }

    .letterhead-text h1 {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 0.04em;
    }

    .letterhead-text p {
        margin: 4px 0 0;
        font-size: 11px;
        color: #334155;
        line-height: 1.4;
    }

    .preview-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.08);
    }

    .summary-card h4 {
        margin: 0 0 8px;
        font-size: 12px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }

    .summary-card p {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
    }

    .letterhead-section .company-address {
        font-size: 12px;
        color: #475569;
        margin-bottom: 2px;
        line-height: 1.4;
    }

    .hide-for-preview {
        display: none !important;
    }

    .preview-modal.show {
        z-index: 99999 !important;
    }
</style>

    <div class="page-title">
        <h1>Laporan Sistem Preventive Maintenance</h1>
        <!-- <p>Gunakan filter berikut untuk menampilkan laporan lalu ekspor atau cetak.</p> -->
    </div>

    <div class="form-card">
        <h2>Filter Laporan</h2>
        <div class="form-grid">
            <div class="input-group">
                <label for="jenisLaporan">Jenis Laporan</label>
                <select id="jenisLaporan">
                    <option value="">Pilih jenis laporan</option>
                    <option value="data-aset">Data Aset</option>
                    <option value="kerusakan">Kerusakan</option>
                    <option value="analisis-rcm">Analisis Risiko Kerusakan</option>
                    <option value="jadwal-maintenance">Jadwal Maintenance</option>
                    <option value="estimasi-biaya">Estimasi Biaya</option>
                    <option value="riwayat-pekerjaan">Riwayat Pekerjaan</option>
                    <option value="gabungan">Gabungan</option>
                </select>
            </div>

            <div class="input-group">
                <label for="tanggalAwal">Tanggal Awal</label>
                <input type="date" id="tanggalAwal">
            </div>

            <div class="input-group">
                <label for="tanggalAkhir">Tanggal Akhir</label>
                <input type="date" id="tanggalAkhir">
            </div>

            <div class="input-group">
                <label for="formatOutput">Format Output</label>
                <select id="formatOutput">
                    <option value="">Pilih format</option>
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                </select>
            </div>
        </div>

        <div class="button-area">
            <button type="button" class="btn-submit" onclick="loadPreview()">Tampilkan Laporan</button>
            <button type="button" class="btn-reset" onclick="resetPreview()">Reset</button>
        </div>
    </div>

    <!-- Live Preview Modal -->
    <div id="previewModal" class="preview-modal">
        <div class="preview-content">
            <div class="preview-header">
                <div>
                    <h2 id="previewTitle">Laporan</h2>
                    <p id="previewDesc" style="margin: 8px 0 0 0; color: #64748b; font-size: 14px;"></p>
                </div>
                <button type="button" class="preview-close" onclick="closePreviewModal()">&times;</button>
            </div>

            <div class="preview-body" id="previewSection">
                <div id="previewSummary" class="preview-summary"></div>
                <div id="previewTableContainer"></div>
                <div id="previewChartContainer" style="display:none; margin-top:24px;">
                    <h3 style="margin-bottom: 16px; color: #0f172a;">Visualisasi Data</h3>
                    <div style="height: 300px; position: relative;">
                        <canvas id="previewChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="preview-footer">
                <button type="button" class="btn-reset" onclick="closePreviewModal()">Batal</button>
                <button type="button" id="confirmPdfButton" class="btn-print" onclick="downloadPdf()" style="display:none;">Cetak PDF</button>
                <button type="button" id="confirmExcelButton" class="btn-export" onclick="exportExcel()" style="display:none;">Ekspor Excel</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const apiUrl = '/api/laporan';

    function closePreviewModal() {
        const modal = document.getElementById('previewModal');
        modal.classList.remove('show');

        // tampilkan kembali sidebar & navbar
        document.querySelector('.sidebar')?.classList.remove('hide-for-preview');
        document.querySelector('.topbar')?.classList.remove('hide-for-preview');
        document.querySelector('.header')?.classList.remove('hide-for-preview');
        document.querySelector('.navbar')?.classList.remove('hide-for-preview');

        if (window.previewChartInstance) {
            window.previewChartInstance.destroy();
            window.previewChartInstance = null;
        }
    }

    function buildTable(columns, rows) {
        if (!rows || rows.length === 0) return '<p style="color: #64748b;">Tidak ada data laporan pada periode yang dipilih.</p>';
        let html = '<table><thead><tr>';
        columns.forEach(c => html += `<th>${c}</th>`);
        html += '</tr></thead><tbody>';
        rows.forEach(r => {
            html += '<tr>';
            r.forEach(cell => html += `<td>${cell}</td>`);
            html += '</tr>';
        });
        html += '</tbody></table>';
        return `<div class="table-responsive">${html}</div>`;
    }

    async function loadPreview() {
        const jenis = document.getElementById('jenisLaporan').value;
        const tanggalAwal = document.getElementById('tanggalAwal').value;
        const tanggalAkhir = document.getElementById('tanggalAkhir').value;
        const format = document.getElementById('formatOutput').value;

        if (!jenis) { alert('Pilih jenis laporan terlebih dahulu.'); return; }
        if (!format) { alert('Pilih format output terlebih dahulu.'); return; }

        const modal = document.getElementById('previewModal');
        const tableContainer = document.getElementById('previewTableContainer');
        tableContainer.innerHTML = '<p style="color: #64748b; text-align: center;">Memuat data laporan...</p>';
        modal.classList.add('show');

        // sembunyikan navbar dan sidebar
        document.querySelector('.sidebar')?.classList.add('hide-for-preview');
        document.querySelector('.topbar')?.classList.add('hide-for-preview');
        document.querySelector('.header')?.classList.add('hide-for-preview');
        document.querySelector('.navbar')?.classList.add('hide-for-preview');

        try {
            const params = new URLSearchParams({ jenis, tanggal_awal: tanggalAwal, tanggal_akhir: tanggalAkhir });
            const resp = await fetch(`${apiUrl}?${params.toString()}`);
            const json = await resp.json();
            const report = json.data || json;

            if (!report || !report.rows || report.rows.length === 0) {
                tableContainer.innerHTML = '<p style="color: #64748b; text-align: center;">Tidak ada data laporan pada periode yang dipilih.</p>';
                document.getElementById('previewTitle').textContent = report?.title || 'Laporan';
                document.getElementById('previewDesc').textContent = report?.description || '';
                document.getElementById('confirmPdfButton').style.display = 'none';
                document.getElementById('confirmExcelButton').style.display = 'none';
                document.getElementById('previewChartContainer').style.display = 'none';
                return;
            }

            document.getElementById('previewTitle').textContent = report.title || 'Laporan';
            document.getElementById('previewDesc').textContent = report.description || '';
            document.getElementById('previewSummary').innerHTML = '';
            const summaryContainer = document.getElementById('previewSummary');
            const totalRows = report.rows?.length || 0;
            const itemCountCard = `<div class="summary-card"><h4>Total Baris</h4><p>${totalRows}</p></div>`;
            const titleCard = `<div class="summary-card"><h4>Judul Laporan</h4><p>${report.title || '-'}</p></div>`;
            summaryContainer.innerHTML = itemCountCard + titleCard;
            tableContainer.innerHTML = buildTable(report.columns, report.rows);

            document.getElementById('confirmPdfButton').style.display = (format === 'pdf') ? 'inline-flex' : 'none';
            document.getElementById('confirmExcelButton').style.display = (format === 'excel') ? 'inline-flex' : 'none';

            if (report.chart && typeof Chart !== 'undefined' && report.chart.labels?.length) {
                const ctx = document.getElementById('previewChart').getContext('2d');
                document.getElementById('previewChartContainer').style.display = 'block';
                if (window.previewChartInstance) {
                    window.previewChartInstance.destroy();
                }
                window.previewChartInstance = new Chart(ctx, {
                    type: report.chart.type === 'pie' ? 'doughnut' : report.chart.type || 'bar',
                    data: {
                        labels: report.chart.labels || [],
                        datasets: report.chart.datasets || [],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { font: { size: 12 }, color: '#334155', padding: 16 }
                            }
                        },
                    },
                });
            } else {
                document.getElementById('previewChartContainer').style.display = 'none';
                if (window.previewChartInstance) {
                    window.previewChartInstance.destroy();
                    window.previewChartInstance = null;
                }
            }
        } catch (error) {
            console.error('Error loading preview:', error);
            tableContainer.innerHTML = '<p style="color: #ef4444; text-align: center;">Gagal memuat data laporan. Silakan coba lagi.</p>';
        }
    }

    function resetPreview() {
        document.getElementById('jenisLaporan').value = '';
        document.getElementById('formatOutput').value = '';
        closePreviewModal();
    }

    async function downloadPdf() {
        const jenis = document.getElementById('jenisLaporan').value;
        const tanggalAwal = document.getElementById('tanggalAwal').value;
        const tanggalAkhir = document.getElementById('tanggalAkhir').value;
        if (!jenis) return alert('Pilih jenis laporan.');
        const url = `/laporan/cetak-pdf?jenis=${encodeURIComponent(jenis)}&tanggal_awal=${encodeURIComponent(tanggalAwal)}&tanggal_akhir=${encodeURIComponent(tanggalAkhir)}`;
        window.location = url;
        closePreviewModal();
    }

    function exportExcel() {
        const jenis = document.getElementById('jenisLaporan').value;
        const tanggalAwal = document.getElementById('tanggalAwal').value;
        const tanggalAkhir = document.getElementById('tanggalAkhir').value;
        if (!jenis) return alert('Pilih jenis laporan.');
        const url = `/laporan/export-excel?jenis=${encodeURIComponent(jenis)}&tanggal_awal=${encodeURIComponent(tanggalAwal)}&tanggal_akhir=${encodeURIComponent(tanggalAkhir)}`;
        window.location = url;
        closePreviewModal();
    }

    // Close modal when clicking outside
    document.addEventListener('click', (e) => {
        const modal = document.getElementById('previewModal');
        if (e.target === modal) {
            closePreviewModal();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const now = new Date();
        const oneMonthAgo = new Date(now);
        oneMonthAgo.setMonth(now.getMonth() - 1);
        document.getElementById('tanggalAwal').value = oneMonthAgo.toISOString().split('T')[0];
        document.getElementById('tanggalAkhir').value = now.toISOString().split('T')[0];
    });
</script>
@endpush

