@extends('layouts.app')
@section('title', 'Input Kerusakan')
@php
    $activePage = 'inputkerusakan';
    $sidebarType = 'teknisi';
@endphp
@section('content')
<style>
    .form-card {
        background: #fff;
        padding: 24px;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.08);
    }

    .topbar {
        margin-bottom: 24px;
    }

    .topbar h1 {
        margin: 0 0 8px;
        font-size: 28px;
        color: #0f172a;
    }

    .topbar p {
        margin: 0;
        color: #475569;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .input-group {
        display: flex;
        flex-direction: column;
    }

    .input-group.full {
        grid-column: span 2;
    }

    .input-group label {
        margin-bottom: 8px;
        font-weight: 700;
        color: #1f2937;
    }

    .input-group input,
    .input-group select,
    .input-group textarea {
        width: 100%;
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 12px 14px;
        font-size: 14px;
        background: #f8fafc;
        color: #0f172a;
    }

    .input-group textarea {
        min-height: 120px;
        resize: vertical;
    }

    .button-area {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-cancel,
    .btn-submit {
        border: none;
        border-radius: 14px;
        padding: 12px 24px;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
    }

    .btn-cancel {
        background: #e2e8f0;
        color: #334155;
    }

    .btn-submit {
        background: #2563eb;
        color: #fff;
    }

    @media (max-width: 860px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .input-group.full {
            grid-column: span 1;
        }
    }
</style>

<!-- <div class="topbar">
    <h1>Input Data Kerusakan</h1>
    <p>Form ini digunakan teknisi untuk mencatat kerusakan mesin agar langsung tersedia di halaman admin Data Kerusakan.</p>
</div> -->

<div class="form-card">
    <form id="kerusakanForm">
        <div class="form-grid">
            <div class="input-group">
                <label>Nama Aset</label>
                <select id="assetSelect" required>
                    <option value="">Pilih Nama Aset</option>
                    @foreach($assets as $aset)
                        <option value="{{ $aset->kode_aset }}">{{ $aset->nama_aset }} ({{ $aset->kode_aset }})</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label>Komponen Rusak</label>
                <select id="komponenSelect" required disabled>
                    <option value="">Pilih Komponen</option>
                </select>
            </div>

            <div class="input-group">
                <label>Tanggal Kerusakan</label>
                <input type="date" id="tanggalKerusakan" required>
            </div>

            <div class="input-group">
                <label>Teknisi Pelapor</label>
                <input type="text" id="teknisiPelapor" value="{{ auth()->user()->name ?? '' }}" {{ auth()->check() ? 'readonly' : '' }} placeholder="Nama teknisi pelapor" required>
            </div>

            <div class="input-group">
                <label>Status Kerusakan</label>
                <select id="statusKerusakan" required>
                    <option value="">Pilih Status</option>
                    <option value="Belum Ditangani">Belum Ditangani</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div class="input-group full">
                <label>Jenis Kerusakan</label>
                <input type="text" id="jenisKerusakan" placeholder="Contoh: Bearing aus / Motor overheat" required>
            </div>

            <div class="input-group full">
                <label>Deskripsi Kerusakan</label>
                <textarea id="deskripsiKerusakan" placeholder="Jelaskan dampak dan detail kerusakan"></textarea>
            </div>

            <!-- <div class="input-group full" style="background: #fef3c7; border-radius: 16px; padding: 16px; color: #92400e;">
                <strong>Penting:</strong>
                <p style="margin: 8px 0 0;">Nilai Risiko wajib diisi oleh teknisi berdasarkan hasil pemeriksaan lapangan. Nilai ini digunakan sistem untuk menentukan tingkat prioritas maintenance.</p>
            </div> -->

            <div class="input-group">
                <label>Upload Dokumentasi Kerusakan</label>
                <input type="file" id="fotoKerusakan" accept=".jpg,.jpeg,.png,.webp">
            </div>

            <div class="input-group">
                <label>Downtime (Jam)</label>
                <input type="number" id="downtimeJam" step="0.1" min="0" placeholder="Contoh: 3.5" required>
            </div>

            <div class="input-group">
                <label>Estimasi Biaya</label>
                <input type="number" id="estimasiBiaya" step="0.01" min="0" placeholder="Contoh: 1500000" required>
            </div>

            <div class="input-group full">
                <label>Catatan Teknisi</label>
                <textarea id="catatanTeknisi" placeholder="Tambahkan catatan hasil pengecekan teknisi"></textarea>
            </div>
        </div>

        <div class="button-area">
            <button type="reset" class="btn-cancel">Batal</button>
            <button type="submit" class="btn-submit">Simpan Data</button>
        </div>
    </form>
</div>

<script>
    const assetSelect = document.getElementById('assetSelect');
    const komponenSelect = document.getElementById('komponenSelect');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    assetSelect.addEventListener('change', function () {
        const kodeAset = this.value;
        komponenSelect.innerHTML = '<option value="">Memuat komponen...</option>';
        komponenSelect.disabled = true;

        if (!kodeAset) {
            komponenSelect.innerHTML = '<option value="">Pilih Komponen</option>';
            komponenSelect.disabled = true;
            return;
        }

        fetch(`/api/komponen/${kodeAset}`)
            .then(response => response.json())
            .then(result => {
                if (result.success && Array.isArray(result.data)) {
                    komponenSelect.innerHTML = '<option value="">Pilih Komponen</option>' + result.data.map(k => `
                        <option value="${k.kode_komponen}">${k.nama_komponen} (${k.kode_komponen})</option>
                    `).join('');
                    komponenSelect.disabled = false;
                } else {
                    komponenSelect.innerHTML = '<option value="">Komponen tidak ditemukan</option>';
                    showToast('Gagal memuat komponen.', 'error');
                }
            })
            .catch(() => {
                komponenSelect.innerHTML = '<option value="">Komponen tidak ditemukan</option>';
                showToast('Gagal memuat komponen.', 'error');
            });
    });

    document.getElementById('kerusakanForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const fotoInput = document.getElementById('fotoKerusakan');
        if (fotoInput.files.length > 0) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(fotoInput.files[0].type)) {
                showToast('Format file harus JPG, JPEG, PNG, atau WEBP.', 'error');
                return;
            }
        }

        const formData = new FormData();
        formData.append('tanggal_kerusakan', document.getElementById('tanggalKerusakan').value);
        formData.append('kode_aset', assetSelect.value);
        formData.append('kode_komponen', komponenSelect.value);
        formData.append('jenis_kerusakan', document.getElementById('jenisKerusakan').value.trim());
        formData.append('deskripsi_kerusakan', document.getElementById('deskripsiKerusakan').value.trim());
        formData.append('downtime_jam', document.getElementById('downtimeJam').value);
        formData.append('teknisi_pelapor', document.getElementById('teknisiPelapor').value.trim());
        formData.append('status', document.getElementById('statusKerusakan').value || 'Belum Ditangani');
        formData.append('estimasi_biaya', document.getElementById('estimasiBiaya').value);
        formData.append('catatan_teknisi', document.getElementById('catatanTeknisi').value.trim());

        if (fotoInput.files.length > 0) {
            formData.append('foto_kerusakan', fotoInput.files[0]);
        }

        fetch('/api/kerusakan', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData,
        })
            .then(async response => {
                const result = await response.json();
                if (!response.ok) {
                    let pesan = result.message || 'Gagal menyimpan data kerusakan.';
                    if (result.errors) {
                        pesan = Object.values(result.errors).flat().join('\n');
                    }
                    showToast(pesan, 'error');
                    return;
                }

                showToast(result.message || 'Data kerusakan berhasil disimpan.', 'success');
                this.reset();
                komponenSelect.innerHTML = '<option value="">Pilih Komponen</option>';
                komponenSelect.disabled = true;
            })
            .catch(error => {
                console.error(error);
                showToast('Terjadi kesalahan pada server.', 'error');
            });
    });
</script>
@endsection
