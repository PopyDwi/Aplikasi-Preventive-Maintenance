@extends('layouts.app')
@section('title', 'Analisis Risiko Kerusakan')
@php
    $activePage = 'inputrisikokerusakan';
    $sidebarType = 'teknisi';
@endphp
@section('content')
<style>
    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.08);
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .form-subtitle {
        color: #475569;
        margin-bottom: 24px;
        max-width: 720px;
        line-height: 1.6;
    }

    .form-grid {
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .input-group {
        display: flex;
        flex-direction: column;
    }

    .input-group.full {
        grid-column: span 2;
    }

    .input-label {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .input-label span {
        color: #dc2626;
    }

    .input-field,
    .input-field textarea,
    .input-field select {
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        border-radius: 14px;
        padding: 12px 14px;
        font-size: 14px;
        color: #0f172a;
        width: 100%;
        box-sizing: border-box;
    }

    .input-field textarea {
        min-height: 110px;
        resize: vertical;
    }

    .help-text {
        margin-top: 8px;
        color: #475569;
        font-size: 13px;
    }

    .help-text.important {
        color: #b91c1c;
        font-size: 13px;
        margin-top: 6px;
    }

    .info-box {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 16px;
        padding: 18px 20px;
        color: #92400e;
        margin-bottom: 24px;
        line-height: 1.7;
    }

    .button-area {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-submit,
    .btn-reset {
        border: none;
        border-radius: 14px;
        padding: 12px 24px;
        font-weight: 700;
        cursor: pointer;
        font-size: 14px;
    }

    .data-card, .table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.08);
        margin-top: 24px;
    }

    .table-card table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }

    .table-card th,
    .table-card td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: middle;
        font-size: 14px;
    }

    .table-card th {
        background: #f1f5f9;
        font-weight: 700;
        color: #0f172a;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action {
        border: none;
        border-radius: 12px;
        padding: 8px 14px;
        font-size: 13px;
        cursor: pointer;
        transition: background 0.2s ease;
        color: white;
    }

    .btn-action.edit {
        background: #2563eb;
    }

    .btn-action.edit:hover {
        background: #1d4ed8;
    }

    .btn-action.delete {
        background: #ef4444;
    }

    .btn-action.delete:hover {
        background: #dc2626;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.45);
        overflow-y: auto;
    }

    .modal-content {
        background: white;
        margin: 50px auto;
        padding: 24px;
        border-radius: 18px;
        width: 90%;
        max-width: 720px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    .close-btn {
        background: transparent;
        border: none;
        font-size: 32px;
        line-height: 1;
        color: #64748b;
        cursor: pointer;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        color: #1f2937;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 20px;
    }

    .btn-clear {
        background: #e2e8f0;
        color: #334155;
    }

    .btn-save {
        background: #2563eb;
        color: white;
    }

    .btn-clear:hover {
        background: #cbd5e1;
    }

    .btn-save:hover {
        background: #1d4ed8;
    }

    .btn-submit {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-reset {
        background: #e2e8f0;
        color: #334155;
    }

    .btn-submit:hover {
        background: #1d4ed8;
    }

    .btn-reset:hover {
        background: #cbd5e1;
    }

    @media (max-width: 860px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .input-group.full {
            grid-column: span 1;
        }

        .button-area {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="form-card">
    <h1 class="form-title">Analisis Risiko Kerusakan</h1>
    <!-- <p class="form-subtitle">Teknisi mengisi nilai risiko kerusakan di lapangan. Data akan dihitung otomatis dan langsung tersimpan dalam sistem untuk prioritas maintenance.</p>

    <div class="info-box">
        <strong>Penting:</strong> Nilai tingkat dampak, frekuensi kerusakan, dan kemudahan deteksi wajib diisi oleh teknisi. Data ini digunakan sistem untuk menghitung nilai risiko dan menentukan prioritas maintenance.
    </div> -->

    <form id="analisisRisikoForm" method="POST" action="/input-risiko-kerusakan">
        @csrf
        <div class="form-grid">
            <div class="input-group">
                <label class="input-label">Nama Aset <span>*</span></label>
                <select name="kode_aset" id="kodeAset" class="input-field" required>
                    <option value="">Pilih Nama Aset</option>
                    @foreach($assets as $aset)
                        <option value="{{ $aset->kode_aset }}">{{ $aset->nama_aset }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Komponen <span>*</span></label>
                <select name="kode_komponen" id="kodeKomponen" class="input-field" required disabled>
                    <option value="">Pilih Komponen</option>
                </select>
            </div>

            <div class="input-group full">
                <label class="input-label">Jenis Kerusakan <span>*</span></label>
                <input type="text" name="mode_kegagalan" id="modeKegagalan" class="input-field" placeholder="Contoh: Bearing aus / Motor overheat / Kebocoran seal" required>
            </div>

            <div class="input-group full">
                <label class="input-label">Dampak Kerusakan <span>*</span></label>
                <textarea name="dampak_kegagalan" id="dampakKerusakan" class="input-field" placeholder="Jelaskan dampak kerusakan terhadap mesin" required></textarea>
            </div>

            <div class="input-group">
                <label class="input-label">Tingkat Dampak Kerusakan <span>*</span></label>
                <input type="number" name="severity" id="severity" class="input-field" min="1" max="10" placeholder="1 - 10" required>
                <p class="help-text important">* Wajib diisi oleh teknisi berdasarkan hasil pemeriksaan lapangan.</p>
            </div>

            <div class="input-group">
                <label class="input-label">Frekuensi Terjadinya Kerusakan <span>*</span></label>
                <input type="number" name="occurrence" id="occurrence" class="input-field" min="1" max="10" placeholder="1 - 10" required>
            </div>

            <div class="input-group">
                <label class="input-label">Kemudahan Kerusakan Terdeteksi <span>*</span></label>
                <input type="number" name="detection" id="detection" class="input-field" min="1" max="10" placeholder="1 - 10" required>
            </div>
        </div>

        <div id="hasilAnalisis" class="info-box" style="display:none; margin-top:24px;">
            <h2 style="margin:0 0 8px; font-size:18px; color:#92400e;">Hasil Analisis Risiko</h2>
            <div style="display:grid; gap:10px;">
                <div><strong>Nilai Risiko:</strong> <span id="hasilRpn">-</span></div>
                <div><strong>Kategori Risiko:</strong> <span id="hasilKategori">-</span></div>
                <div><strong>Rekomendasi Tindakan:</strong> <span id="hasilRekomendasi">-</span></div>
                <div><strong>Jadwal Berikutnya:</strong> <span id="hasilJadwal">-</span></div>
            </div>
        </div>

        <div class="button-area">
            <button type="reset" class="btn-reset">Reset</button>
            <button type="button" id="hitungButton" class="btn-submit">Hitung Nilai Risiko</button>
            <button type="button" id="simpanButton" class="btn-submit" style="display:none;">Simpan Analisis Risiko</button>
        </div>
    </form>

    <div class="table-card">
        <h2>Daftar Analisis Risiko Kerusakan</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Aset</th>
                        <th>Komponen</th>
                        <th>Jenis Kerusakan</th>
                        <th>RPN</th>
                        <th>Kategori</th>
                        <th>Jadwal Berikutnya</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="analisisTableBody">
                    <tr>
                        <td colspan="8" class="no-data">Memuat data analisis risiko...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="editAnalisisModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Analisis Risiko</h2>
            <button type="button" class="close-btn" onclick="closeAnalisisModal()">&times;</button>
        </div>
        <form id="editAnalisisForm">
            <div class="form-group">
                <label>Nama Aset</label>
                <select id="editKodeAset" class="input-field" required></select>
            </div>
            <div class="form-group">
                <label>Komponen</label>
                <select id="editKodeKomponen" class="input-field" required disabled></select>
            </div>
            <div class="form-group">
                <label>Jenis Kerusakan</label>
                <input type="text" id="editModeKegagalan" class="input-field" required>
            </div>
            <div class="form-group">
                <label>Dampak Kerusakan</label>
                <textarea id="editDampakKerusakan" class="input-field" required></textarea>
            </div>
            <div class="form-group">
                <label>Tingkat Dampak Kerusakan</label>
                <input type="number" id="editSeverity" class="input-field" min="1" max="10" required>
            </div>
            <div class="form-group">
                <label>Frekuensi Terjadinya Kerusakan</label>
                <input type="number" id="editOccurrence" class="input-field" min="1" max="10" required>
            </div>
            <div class="form-group">
                <label>Kemudahan Kerusakan Terdeteksi</label>
                <input type="number" id="editDetection" class="input-field" min="1" max="10" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-clear" onclick="closeAnalisisModal()">Batal</button>
                <button type="button" class="btn-save" onclick="saveAnalisisEdit()">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const kodeAset = document.getElementById('kodeAset');
    const kodeKomponen = document.getElementById('kodeKomponen');
    const form = document.getElementById('analisisRisikoForm');
    const hitungButton = document.getElementById('hitungButton');
    const simpanButton = document.getElementById('simpanButton');
    const hasilAnalisis = document.getElementById('hasilAnalisis');
    const hasilRpn = document.getElementById('hasilRpn');
    const hasilKategori = document.getElementById('hasilKategori');
    const hasilRekomendasi = document.getElementById('hasilRekomendasi');
    const hasilJadwal = document.getElementById('hasilJadwal');
    let currentAnalysis = null;
    let allAnalisis = [];
    const analysisAssets = @json($assets);
    const editModal = document.getElementById('editAnalisisModal');
    const editKodeAset = document.getElementById('editKodeAset');
    const editKodeKomponen = document.getElementById('editKodeKomponen');
    const editModeKegagalan = document.getElementById('editModeKegagalan');
    const editDampakKerusakan = document.getElementById('editDampakKerusakan');
    const editSeverity = document.getElementById('editSeverity');
    const editOccurrence = document.getElementById('editOccurrence');
    const editDetection = document.getElementById('editDetection');
    let editingAnalisisId = null;

    function getKategoriRisiko(rpn) {
        if (rpn >= 300) return 'Sangat Tinggi';
        if (rpn >= 200) return 'Tinggi';
        if (rpn >= 100) return 'Sedang';
        return 'Rendah';
    }

    function getRekomendasi(kategori) {
        if (kategori === 'Sangat Tinggi') {
            return 'Segera lakukan preventive maintenance, inspeksi menyeluruh, dan prioritaskan penggantian/perbaikan komponen.';
        }
        if (kategori === 'Tinggi') {
            return 'Lakukan pemeriksaan rutin, monitoring kondisi komponen, dan jadwalkan preventive maintenance dalam waktu dekat.';
        }
        if (kategori === 'Sedang') {
            return 'Lakukan perawatan berkala, pembersihan, dan pengecekan fungsi komponen.';
        }
        return 'Lakukan monitoring normal dan pemeriksaan visual secara rutin.';
    }

    function getTanggalJadwal(kategori) {
        const hari = {
            'Sangat Tinggi': 7,
            'Tinggi': 14,
            'Sedang': 30,
            'Rendah': 90
        }[kategori] || 90;

        const tanggal = new Date();
        tanggal.setDate(tanggal.getDate() + hari);
        return tanggal.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function formatTanggal(tanggal) {
        if (!tanggal) return '-';

        const parsed = new Date(tanggal);
        if (Number.isNaN(parsed.getTime())) {
            return '-';
        }

        return parsed.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function validateForm() {
        const fields = [
            { el: kodeAset, name: 'Nama Aset' },
            { el: kodeKomponen, name: 'Komponen' },
            { el: document.getElementById('modeKegagalan'), name: 'Jenis Kerusakan' },
            { el: document.getElementById('dampakKerusakan'), name: 'Dampak Kerusakan' },
            { el: document.getElementById('severity'), name: 'Tingkat Dampak Kerusakan' },
            { el: document.getElementById('occurrence'), name: 'Frekuensi Terjadinya Kerusakan' },
            { el: document.getElementById('detection'), name: 'Kemudahan Kerusakan Terdeteksi' }
        ];

        for (const field of fields) {
            if (!field.el.value || field.el.value.toString().trim() === '') {
                window.showToast(`Field ${field.name} wajib diisi.`, 'warning');
                return false;
            }
        }

        const severity = Number(document.getElementById('severity').value);
        const occurrence = Number(document.getElementById('occurrence').value);
        const detection = Number(document.getElementById('detection').value);

        for (const value of [severity, occurrence, detection]) {
            if (!Number.isInteger(value) || value < 1 || value > 10) {
                window.showToast('Nilai harus angka 1 sampai 10.', 'warning');
                return false;
            }
        }

        return true;
    }

    function resetResult() {
        currentAnalysis = null;
        hasilAnalisis.style.display = 'none';
        simpanButton.style.display = 'none';
        hasilRpn.textContent = '-';
        hasilKategori.textContent = '-';
        hasilRekomendasi.textContent = '-';
        hasilJadwal.textContent = '-';
    }

    hitungButton.addEventListener('click', function () {
        if (!validateForm()) {
            return;
        }

        const severity = Number(document.getElementById('severity').value);
        const occurrence = Number(document.getElementById('occurrence').value);
        const detection = Number(document.getElementById('detection').value);
        const rpn = severity * occurrence * detection;
        const kategori = getKategoriRisiko(rpn);
        const rekomendasi = getRekomendasi(kategori);
        const jadwal = getTanggalJadwal(kategori);

        currentAnalysis = {
            kode_aset: kodeAset.value,
            kode_komponen: kodeKomponen.value,
            mode_kegagalan: document.getElementById('modeKegagalan').value.trim(),
            dampak_kegagalan: document.getElementById('dampakKerusakan').value.trim(),
            severity,
            occurrence,
            detection,
            rpn,
            kategori_risiko: kategori,
            rekomendasi_perawatan: rekomendasi,
            jadwal_maintenance_berikutnya: new Date(new Date().setDate(new Date().getDate() + ({'Sangat Tinggi':7,'Tinggi':14,'Sedang':30,'Rendah':90}[kategori] || 90))).toISOString().slice(0,10)
        };

        hasilRpn.textContent = rpn;
        hasilKategori.textContent = kategori;
        hasilRekomendasi.textContent = rekomendasi;
        hasilJadwal.textContent = jadwal;
        hasilAnalisis.style.display = 'block';
        simpanButton.style.display = 'inline-flex';
        window.showToast('Nilai risiko berhasil dihitung. Silakan klik Simpan Analisis Risiko untuk menyimpan data.', 'info');
    });

    simpanButton.addEventListener('click', function () {
        if (!currentAnalysis) {
            window.showToast('Silakan hitung nilai risiko terlebih dahulu.', 'warning');
            return;
        }

        const data = new FormData();
        Object.entries(currentAnalysis).forEach(([key, value]) => {
            data.append(key, value);
        });
        data.append('_token', document.querySelector('input[name="_token"]').value);

        fetch(form.action, {
            method: 'POST',
            body: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                window.showToast('Analisis risiko kerusakan berhasil disimpan.', 'success');
                form.reset();
                kodeKomponen.innerHTML = '<option value="">Pilih Komponen</option>';
                kodeKomponen.disabled = true;
                resetResult();
                loadAnalisisList();
            } else {
                window.showToast(result.message || 'Gagal menyimpan analisis risiko.', 'error');
            }
        })
        .catch(() => {
            window.showToast('Gagal menyimpan analisis risiko.', 'error');
        });
    });

    form.addEventListener('reset', function () {
        resetResult();
    });

    function loadAnalisisList() {
        fetch('/api/analisis-rcm')
            .then(response => response.json())
            .then(result => {
                if (!result.success) {
                    document.getElementById('analisisTableBody').innerHTML = '<tr><td colspan="8" class="no-data">Gagal memuat data analisis risiko.</td></tr>';
                    return;
                }
                allAnalisis = result.data.data || result.data || [];
                updateAnalisisTable();
            })
            .catch(() => {
                document.getElementById('analisisTableBody').innerHTML = '<tr><td colspan="8" class="no-data">Gagal memuat data analisis risiko.</td></tr>';
            });
    }

    function updateAnalisisTable() {
        const tbody = document.getElementById('analisisTableBody');
        if (!allAnalisis.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="no-data">Belum ada data analisis risiko.</td></tr>';
            return;
        }

        tbody.innerHTML = allAnalisis.map((item, index) => {
            const kategori = item.kategori_risiko || getKategoriRisiko(item.rpn || 0);
            const jadwalRaw = item.jadwal_maintenance_berikutnya || item.tanggal_jadwal_berikutnya || '-';
            const jadwal = jadwalRaw === '-' ? '-' : formatTanggal(jadwalRaw);
            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nama_aset || item.aset?.nama_aset || item.kode_aset || '-'}</td>
                    <td>${item.nama_komponen || item.komponen_rel?.nama_komponen || item.komponenRel?.nama_komponen || item.kode_komponen || '-'}</td>
                    <td>${item.mode_kegagalan || '-'}</td>
                    <td>${item.rpn ?? '-'}</td>
                    <td>${kategori}</td>
                    <td>${jadwal}</td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="btn-action edit" onclick="openAnalisisEditModal(${item.id})">Edit</button>
                            <button type="button" class="btn-action delete" onclick="deleteAnalisis(${item.id})">Hapus</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function openAnalisisEditModal(id) {
        const item = allAnalisis.find(entry => Number(entry.id) === Number(id));
        if (!item) return;

        editingAnalisisId = id;

        editKodeAset.innerHTML = '<option value="">Pilih Nama Aset</option>' + analysisAssets.map(asset => `
            <option value="${asset.kode_aset}" ${asset.kode_aset === item.kode_aset ? 'selected' : ''}>${asset.nama_aset}</option>
        `).join('');

        editKodeKomponen.innerHTML = '<option value="">Pilih Komponen</option>';
        editKodeKomponen.disabled = true;

        if (item.kode_aset) {
            fetch(`/api/komponen/${item.kode_aset}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && Array.isArray(data.data)) {
                        editKodeKomponen.innerHTML = '<option value="">Pilih Komponen</option>' + data.data.map(komponen => `
                            <option value="${komponen.kode_komponen}" ${komponen.kode_komponen === item.kode_komponen ? 'selected' : ''}>${komponen.nama_komponen}</option>
                        `).join('');
                        editKodeKomponen.disabled = false;
                    }
                });
        }

        editModeKegagalan.value = item.mode_kegagalan || '';
        editDampakKerusakan.value = item.dampak_kegagalan || '';
        editSeverity.value = item.severity || '';
        editOccurrence.value = item.occurrence || '';
        editDetection.value = item.detection || '';

        editModal.style.display = 'block';
    }

    function closeAnalisisModal() {
        editModal.style.display = 'none';
        editingAnalisisId = null;
    }

    editKodeAset.addEventListener('change', function () {
        const assetId = this.value;
        editKodeKomponen.disabled = true;
        editKodeKomponen.innerHTML = '<option value="">Memuat komponen...</option>';

        if (!assetId) {
            editKodeKomponen.innerHTML = '<option value="">Pilih Komponen</option>';
            editKodeKomponen.disabled = true;
            return;
        }

        fetch(`/api/komponen/${assetId}`)
            .then(response => response.json())
            .then(result => {
                if (!result.success || !Array.isArray(result.data)) {
                    editKodeKomponen.innerHTML = '<option value="">Komponen tidak ditemukan</option>';
                    editKodeKomponen.disabled = true;
                    return;
                }
                editKodeKomponen.innerHTML = '<option value="">Pilih Komponen</option>' + result.data.map(item => `
                    <option value="${item.kode_komponen}">${item.nama_komponen}</option>
                `).join('');
                editKodeKomponen.disabled = false;
            })
            .catch(() => {
                editKodeKomponen.innerHTML = '<option value="">Komponen tidak ditemukan</option>';
                editKodeKomponen.disabled = true;
            });
    });

    function saveAnalisisEdit() {
        if (!editingAnalisisId) return;

        const kodeAsetValue = editKodeAset.value;
        const kodeKomponenValue = editKodeKomponen.value;
        const modeKegagalanValue = editModeKegagalan.value.trim();
        const dampakValue = editDampakKerusakan.value.trim();
        const severityValue = Number(editSeverity.value);
        const occurrenceValue = Number(editOccurrence.value);
        const detectionValue = Number(editDetection.value);

        if (!kodeAsetValue || !kodeKomponenValue || !modeKegagalanValue || !dampakValue || !severityValue || !occurrenceValue || !detectionValue) {
            window.showToast('Lengkapi semua field edit sebelum menyimpan.', 'warning');
            return;
        }

        // Debug logs to inspect payload and id
        const payload = {
            kode_aset: kodeAsetValue,
            kode_komponen: kodeKomponenValue,
            mode_kegagalan: modeKegagalanValue,
            dampak_kegagalan: dampakValue,
            severity: severityValue,
            occurrence: occurrenceValue,
            detection: detectionValue
        };

        console.log('Editing ID:', editingAnalisisId);
        console.log('Payload:', payload);

        fetch(`/api/analisis-rcm/${editingAnalisisId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(result => {
            console.log('Update response:', result);
            if (result.success) {
                window.showToast('Analisis risiko berhasil diperbarui.', 'success');
                closeAnalisisModal();
                loadAnalisisList();
            } else {
                window.showToast(result.message || 'Gagal memperbarui analisis risiko.', 'error');
            }
        })
        .catch(err => {
            console.error('Update error:', err);
            window.showToast('Gagal memperbarui analisis risiko.', 'error');
        });
    }

    function deleteAnalisis(id) {
        if (!confirm('Yakin ingin menghapus analisis risiko ini beserta jadwal maintenance terkait?')) {
            return;
        }

        fetch(`/api/analisis-rcm/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                window.showToast('Analisis risiko dan jadwal terkait berhasil dihapus.', 'success');
                loadAnalisisList();
            } else {
                window.showToast(result.message || 'Gagal menghapus analisis risiko.', 'error');
            }
        })
        .catch(() => {
            window.showToast('Gagal menghapus analisis risiko.', 'error');
        });
    }

    function loadAsetKomponenOptions() {
        kodeAset.dispatchEvent(new Event('change'));
    }

    kodeAset.addEventListener('change', function () {
        const aset = this.value;
        kodeKomponen.innerHTML = '<option value="">Memuat komponen...</option>';
        kodeKomponen.disabled = true;

        if (!aset) {
            kodeKomponen.innerHTML = '<option value="">Pilih Komponen</option>';
            kodeKomponen.disabled = true;
            return;
        }

        fetch(`/api/komponen/${aset}`)
            .then(response => response.json())
            .then(result => {
                if (result.success && Array.isArray(result.data)) {
                    kodeKomponen.innerHTML = '<option value="">Pilih Komponen</option>' + result.data.map(item => `
                        <option value="${item.kode_komponen}">${item.nama_komponen}</option>
                    `).join('');
                    kodeKomponen.disabled = false;
                } else {
                    kodeKomponen.innerHTML = '<option value="">Komponen tidak ditemukan</option>';
                    kodeKomponen.disabled = true;
                }
            })
            .catch(() => {
                kodeKomponen.innerHTML = '<option value="">Komponen tidak ditemukan</option>';
                kodeKomponen.disabled = true;
            });
    });

    document.addEventListener('DOMContentLoaded', function() {
        loadAnalisisList();
    });
</script>
@endsection
