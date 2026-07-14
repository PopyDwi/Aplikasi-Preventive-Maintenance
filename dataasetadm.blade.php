@extends('layouts.app')
@section('title', 'Data Aset')
@php
    $activePage = 'dataaset';
    $sidebarType = 'admin';
@endphp
@section('content')
<div class="content-card">
    <div class="page-title">
        <h1>Data Aset</h1>
        <!-- <p>Kelola aset dan komponen terkait dalam satu halaman yang lebih sederhana.</p> -->
    </div>

    <div class="action-bar">
        <input id="searchInput" type="text" class="search-input" placeholder="Cari kode aset / nama aset...">
        <button id="toggleFormBtn" type="button" class="btn-add" onclick="toggleAssetForm()">+ Tambah Aset</button>
    </div>

    <div id="assetFormCard" class="content-card" style="display:none; margin-top:20px;">
        <div class="form-header">
            <div>
                <h3 id="formTitle">Tambah Aset Baru</h3>
                <p class="muted-text">Masukkan informasi aset dan tambahkan komponen yang terhubung ke aset.</p>
            </div>
        </div>

        <div class="form-grid">
            <div class="input-group">
                <label>Kode Aset</label>
                <input id="assetKode" name="kode_aset" type="text" class="input" readonly>
                <div class="input-error-message"></div>
            </div>
            <div class="input-group">
                <label>Nama Aset</label>
                <input id="assetNama" name="nama_aset" type="text" class="input" placeholder="Contoh: Pompa Distribusi Air Bersih">
                <div class="input-error-message"></div>
            </div>
            <div class="input-group">
                <label>Penanggung Jawab</label>
                <select id="assetPenanggungJawab" name="penanggungjawab" class="input">
                    <option value="">Pilih Teknisi</option>
                    @foreach($teknisis as $teknisi)
                        <option value="{{ $teknisi->name }}">{{ $teknisi->name }}</option>
                    @endforeach
                </select>
                <div class="input-error-message"></div>
            </div>
            <div class="input-group">
                <label>Status</label>
                <select id="assetStatus" name="status" class="input">
                    <option value="Normal">Normal</option>
                    <option value="Warning">Warning</option>
                    <option value="Kritis">Kritis</option>
                </select>
                <div class="input-error-message"></div>
            </div>
            <div class="input-group">
                <label>Tanggal Instalasi</label>
                <input id="assetTanggal" name="tanggal_instalasi" type="date" class="input">
                <div class="input-error-message"></div>
            </div>
        </div>

        <div class="component-section">
            <div class="component-header">
                <h4>Data Komponen</h4>
                <button type="button" class="btn-add btn-sm" onclick="addComponentRow()">+ Tambah Komponen</button>
            </div>
            <div class="table-container component-table-card">
                <table class="component-table">
                    <thead>
                        <tr>
                            <th>Kode Komponen</th>
                            <th>Nama Komponen</th>
                            <th>Fungsi / Keterangan</th>
                            <th>Volume</th>
                            <th>Satuan</th>
                            <th>Harga Satuan</th>
                            <th>Total Biaya</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="componentRows"></tbody>
                </table>
            </div>
        </div>

        <div class="button-area">
            <button type="button" class="btn-submit" onclick="saveAsset()">Simpan Aset</button>
        </div>
    </div>

    <div class="table-container" style="margin-top:20px;">
        <table id="assetTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Aset</th>
                    <th>Nama Aset</th>
                    <th>Penanggung Jawab</th>
                    <th>Status</th>
                    <th>Tanggal Instalasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div id="assetDetailCard" class="content-card" style="display:none; margin-top:18px;">
    <div class="report-header">
        <div>
            <h2 id="detailTitle">Detail Aset</h2>
            <p id="detailSubtitle" class="muted-text">Informasi lengkap aset dan komponen yang terhubung.</p>
        </div>
        <button type="button" class="btn-reset" onclick="closeDetail()">Tutup</button>
    </div>

    <div class="asset-summary-grid">
        <div><strong>Kode Aset</strong><p id="detailKode"></p></div>
        <div><strong>Nama Aset</strong><p id="detailNama"></p></div>
        <div><strong>Penanggung Jawab</strong><p id="detailPenanggungJawab"></p></div>
        <div><strong>Status</strong><p id="detailStatus"></p></div>
        <div><strong>Tanggal Instalasi</strong><p id="detailTanggal"></p></div>
    </div>

    <div class="table-container" style="margin-top:20px;">
        <h3>Komponen Terhubung</h3>
        <table id="detailComponentTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Komponen</th>
                    <th>Nama Komponen</th>
                    <th>Fungsi / Keterangan</th>
                    <th>Volume</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Total Biaya</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<style>
    .form-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        margin-bottom: 20px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .component-section {
        margin-bottom: 20px;
    }

    .component-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        gap: 16px;
    }

    .component-table-card {
        padding: 0;
        background: transparent;
        box-shadow: none;
    }

    .component-table {
        width: 100%;
        border-collapse: collapse;
    }

    .component-table th,
    .component-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .component-table th {
        background: #f8fafc;
        font-weight: 700;
        color: #334155;
    }

    .asset-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .asset-summary-grid div {
        background: #f8fafc;
        padding: 16px;
        border-radius: 16px;
    }

    .asset-summary-grid strong {
        display: block;
        margin-bottom: 8px;
        color: #334155;
    }

    .btn-add,
    .btn-detail,
    .btn-edit,
    .btn-delete,
    .btn-sm {
        border: none;
        border-radius: 14px;
        padding: 12px 16px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s ease;
    }

    .btn-add {
        background: #2563eb;
        color: white;
    }

    .btn-add.btn-sm {
        padding: 8px 12px;
        font-size: 13px;
    }

    .btn-detail,
    .btn-edit {
        background: #3b82f6;
        color: white;
        margin-right: 8px;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .table-container table th,
    .table-container table td {
        padding: 12px 14px;
    }

    @media (max-width: 1000px) {
        .form-grid,
        .asset-summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@push('scripts')
<script>
    const assets = @json($assets->toArray());
    const nextKodeAset = @json($nextKodeAset);
    const nextKodeKomponen = @json($nextKodeKomponen);

    let editingAssetKode = null;
    let nextAsetIndex = parseInt(String(nextKodeAset).replace(/\D/g, ''), 10) || 1;
    let nextKomponenIndex = parseInt(String(nextKodeKomponen).replace(/\D/g, ''), 10) || 1;

    function generateAsetCode() {
        return 'AST' + String(nextAsetIndex++).padStart(3, '0');
    }

    function generateKomponenCode() {
        return 'KMP' + String(nextKomponenIndex++).padStart(3, '0');
    }

    function formatCurrency(value) {
        const number = Number(value) || 0;
        return number.toFixed(2);
    }

    function renderAssetTable(filter = '') {
        const tbody = document.querySelector('#assetTable tbody');
        const query = filter.trim().toLowerCase();

        tbody.innerHTML = assets
            .filter(asset => {
                return !query ||
                    asset.kode_aset.toLowerCase().includes(query) ||
                    asset.nama_aset.toLowerCase().includes(query) ||
                    (asset.penanggungjawab || '').toLowerCase().includes(query) ||
                    (asset.status || '').toLowerCase().includes(query);
            })
            .map((asset, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${asset.kode_aset}</td>
                    <td>${asset.nama_aset}</td>
                    <td>${asset.penanggungjawab || '-'}</td>
                    <td>${formatStatus(asset.status)}</td>
                    <td>${asset.tanggal_instalasi || '-'}</td>
                    <td>
                        <button type="button" class="btn-detail" onclick="showAssetDetail(${asset.id})">Detail</button>
                        <button type="button" class="btn-edit" onclick="toggleForm(true, ${asset.id})">Edit</button>
                        <button type="button" class="btn-delete" onclick="deleteAsset(${asset.id})">Hapus</button>
                    </td>
                </tr>`)
            .join('');
    }

    function formatStatus(status) {
        const normalized = String(status || 'Normal').toLowerCase();
        const mapping = {
            normal: 'status selesai',
            warning: 'status warning',
            critical: 'status kritis',
            'perlu monitoring': 'status perlu-monitoring',
            baik: 'status baik',
            kritis: 'status kritis'
        };
        const className = mapping[normalized] || 'status';
        return `<span class="${className}">${status || 'Normal'}</span>`;
    }

    function toggleAssetForm() {
        const formCard = document.getElementById('assetFormCard');
        const isOpen = formCard.style.display === 'block';
        toggleForm(!isOpen);
    }

    function toggleForm(show, assetId = null) {
        const formCard = document.getElementById('assetFormCard');
        const toggleBtn = document.getElementById('toggleFormBtn');
        const title = document.getElementById('formTitle');

        if (!show) {
            editingAssetKode = null;
            formCard.style.display = 'none';
            toggleBtn.textContent = '+ Tambah Aset';
            resetForm();
            return;
        }

        formCard.style.display = 'block';
        toggleBtn.textContent = 'Tutup Formulir';

        if (assetId) {
            const asset = assets.find(item => item.id === assetId);
            if (!asset) return;
            editingAssetKode = asset.kode_aset;
            title.textContent = 'Edit Aset';
            document.getElementById('assetKode').value = asset.kode_aset;
            document.getElementById('assetNama').value = asset.nama_aset;
            document.getElementById('assetPenanggungJawab').value = asset.penanggungjawab || '';
            document.getElementById('assetStatus').value = asset.status || 'Normal';
            document.getElementById('assetTanggal').value = asset.tanggal_instalasi || '';
            document.getElementById('componentRows').innerHTML = '';
            (asset.komponen || []).forEach(component => addComponentRow(component)); //ini loh yang di ganti
        } else {
            editingAssetKode = null;
            title.textContent = 'Tambah Aset Baru';
            resetForm();
        }
    }

    function resetForm() {
        document.getElementById('assetKode').value = generateAsetCode();
        document.getElementById('assetNama').value = '';
        document.getElementById('assetPenanggungJawab').value = '';
        document.getElementById('assetStatus').value = 'Normal';
        document.getElementById('assetTanggal').value = '';
        document.getElementById('componentRows').innerHTML = '';
        addComponentRow();
    }

    function addComponentRow(data = {}) {
        const tbody = document.getElementById('componentRows');
        const row = document.createElement('tr');
        row.className = 'component-input-row';
        const kodeKomponen = data.kode_komponen || generateKomponenCode();

        row.innerHTML = `
            <td><input type="text" class="input kode-komponen-input" readonly value="${kodeKomponen}" /></td>
            <td><input type="text" class="input nama-komponen-input" placeholder="Nama Komponen" value="${data.nama_komponen || ''}" /></td>
            <td><input type="text" class="input fungsi-komponen-input" placeholder="Fungsi / Keterangan" value="${data.fungsi_keterangan || ''}" /></td>
            <td><input type="number" min="0" class="input volume-input" placeholder="Volume" value="${data.volume ?? ''}" oninput="updateRowTotal(this)" /></td>
            <td><input type="text" class="input satuan-input" placeholder="Satuan" value="${data.satuan || ''}" /></td>
            <td><input type="number" min="0" step="0.01" class="input harga-input" placeholder="Harga Satuan" value="${data.harga_satuan ?? ''}" oninput="updateRowTotal(this)" /></td>
            <td><input type="text" class="input total-biaya-input" readonly value="${formatCurrency(data.total_biaya ?? 0)}" /></td>
            <td><button type="button" class="btn-delete" onclick="removeComponentRow(this)">Hapus</button></td>
        `;

        tbody.appendChild(row);
    }

    function updateRowTotal(input) {
        const row = input.closest('tr');
        if (!row) return;
        const volume = Number(row.querySelector('.volume-input').value) || 0;
        const harga = Number(row.querySelector('.harga-input').value) || 0;
        const total = volume * harga;
        row.querySelector('.total-biaya-input').value = formatCurrency(total);
    }

    function removeComponentRow(button) {
        const row = button.closest('tr');
        if (row) row.remove();
    }

    async function saveAsset() {
        // Reset semua error state
        document.querySelectorAll('.input').forEach(el => el.classList.remove('error'));
        document.querySelectorAll('.input-error-message').forEach(el => el.textContent = '');

        const assetNamaInput = document.getElementById('assetNama');
        const assetTanggalInput = document.getElementById('assetTanggal');
        const componentRows = document.querySelectorAll('.component-input-row');

        let hasAssetError = false;

        // Validasi data aset
        if (!assetNamaInput.value.trim()) {
            assetNamaInput.classList.add('error');
            assetNamaInput.parentElement.querySelector('.input-error-message').textContent = 'Nama aset wajib diisi.';
            hasAssetError = true;
        }
        if (!assetTanggalInput.value) {
            assetTanggalInput.classList.add('error');
            assetTanggalInput.parentElement.querySelector('.input-error-message').textContent = 'Tanggal instalasi wajib diisi.';
            hasAssetError = true;
        }

        if (hasAssetError) {
            showToast('Lengkapi data aset terlebih dahulu.', 'warning');
            return;
        }

        // Validasi komponen ada
        if (componentRows.length === 0) {
            showToast('Tambahkan minimal satu komponen.', 'warning');
            return;
        }

        // Validasi setiap komponen
        let hasComponentError = false;
        const komponen = [];
        
        componentRows.forEach(row => {
            const namaInput = row.querySelector('.nama-komponen-input');
            const volumeInput = row.querySelector('.volume-input');
            const satuanInput = row.querySelector('.satuan-input');
            const hargaInput = row.querySelector('.harga-input');
            
            const namaKomponen = namaInput.value.trim();
            const volume = Number(volumeInput.value) || 0;
            const satuan = satuanInput.value.trim();
            const harga = Number(hargaInput.value) || 0;

            if (!namaKomponen || volume <= 0 || !satuan || harga <= 0) {
                if (!namaKomponen) namaInput.classList.add('error');
                if (volume <= 0) volumeInput.classList.add('error');
                if (!satuan) satuanInput.classList.add('error');
                if (harga <= 0) hargaInput.classList.add('error');
                hasComponentError = true;
                return;
            }

            komponen.push({
                kode_komponen: row.querySelector('.kode-komponen-input').value.trim(),
                nama_komponen: namaKomponen,
                fungsi_keterangan: row.querySelector('.fungsi-komponen-input').value.trim(),
                volume,
                satuan,
                harga_satuan: harga,
                total_biaya: volume * harga,
            });
        });

        if (hasComponentError) {
            showToast('Lengkapi data komponen terlebih dahulu.', 'warning');
            return;
        }

        // Simpan data
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = editingAssetKode ? `/dataaset/${encodeURIComponent(editingAssetKode)}` : '/dataaset';
        const method = editingAssetKode ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                kode_aset: document.getElementById('assetKode').value.trim(),
                nama_aset: assetNamaInput.value.trim(),
                penanggungjawab: document.getElementById('assetPenanggungJawab').value.trim(),
                status: document.getElementById('assetStatus').value,
                tanggal_instalasi: assetTanggalInput.value,
                komponen,
            }),
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            showToast(errorData?.message || 'Gagal memproses aset.', 'error');
            return;
        }

        showToast(editingAssetKode ? 'Aset berhasil diperbarui.' : 'Aset berhasil disimpan.', 'success');
        setTimeout(() => window.location.reload(), 1000);
    }

    function showAssetDetail(id) {
        const asset = assets.find(item => item.id === id);
        if (!asset) return;
        document.getElementById('detailTitle').textContent = `Detail Aset ${asset.kode_aset}`;
        document.getElementById('detailKode').textContent = asset.kode_aset;
        document.getElementById('detailNama').textContent = asset.nama_aset;
        document.getElementById('detailPenanggungJawab').textContent = asset.penanggungjawab || '-';
        document.getElementById('detailStatus').innerHTML = formatStatus(asset.status);
        document.getElementById('detailTanggal').textContent = asset.tanggal_instalasi || '-';

        const tbody = document.querySelector('#detailComponentTable tbody');

        const komponenList = asset.komponen || [];

            tbody.innerHTML = komponenList.length
                ? komponenList.map((component, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${component.kode_komponen}</td>
                    <td>${component.nama_komponen}</td>
                    <td>${component.fungsi_keterangan || '-'}</td>
                    <td>${component.volume || '-'}</td>
                    <td>${component.satuan || '-'}</td>
                    <td>${formatCurrency(component.harga_satuan)}</td>
                    <td>${formatCurrency(component.total_biaya)}</td>
                </tr>
            `).join('')
            : `<tr><td colspan="8">Belum ada komponen</td></tr>`;

        document.getElementById('assetDetailCard').style.display = 'block';
        window.scrollTo({ top: document.getElementById('assetDetailCard').offsetTop - 80, behavior: 'smooth' });
    }

    function closeDetail() {
        document.getElementById('assetDetailCard').style.display = 'none';
    }

    async function deleteAsset(id) {
        const asset = assets.find(item => item.id === id);
        if (!asset || !confirm('Hapus aset ini dari sistem?')) return;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch(`/dataaset/${encodeURIComponent(asset.kode_aset)}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
        });

        if (!response.ok) {
            showToast('Gagal memproses aset.', 'error');
            return;
        }

        showToast('Aset berhasil dihapus.', 'success');
        setTimeout(() => window.location.reload(), 1000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderAssetTable();
        addComponentRow();
        document.getElementById('searchInput').addEventListener('input', event => renderAssetTable(event.target.value));
    });
</script>
@endpush
@endsection
