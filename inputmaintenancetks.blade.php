@extends('layouts.app')
@section('title', 'Input Maintenance')
@php
    $activePage = 'inputmaintenance';
    $sidebarType = 'teknisi';
@endphp
@section('content')
<!-- <div class="topbar">
            <h1>Input Maintenance</h1>
            <p>
                Form ini digunakan teknisi untuk mencatat hasil maintenance dan perbaikan mesin.
            </p>
        </div> -->
<style>

#fileName{
    color:#64748b;
    font-size:14px;
    font-weight:500;
}

#durasiJam,
#biayaMaintenance{
    width:100%;
    height:52px;

    border:1px solid #dbe4f0;
    border-radius:14px;

    padding:0 16px;
    font-size:15px;

    transition:.3s;
}

#durasiJam:focus,
#biayaMaintenance:focus{
    outline:none;

    border-color:#4f7df3;

    box-shadow:
        0 0 0 4px
        rgba(79,125,243,.12);
}

.button-area{
    display:flex;
    justify-content:flex-end;
    gap:12px;
    margin-top:30px;
}
</style>
        <div class="form-card">
            <form id="formPekerjaan" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="input-group">
                        <label>Jadwal Maintenance</label>
                        <select id="selectJadwal">
                            <option value="">Pilih Jadwal yang Akan Dikerjakan</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Nama Aset</label>
                        <input type="text" id="namaAset" readonly>
                    </div>

                    <div class="input-group">
                        <label>Nama Komponen</label>
                        <input type="text" id="namaKomponen" readonly>
                    </div>

                    <div class="input-group">
                        <label>Penanggung Jawab</label>
                        <input type="text" id="penanggungJawab" readonly>
                    </div>

                    <div class="input-group">
                        <label>Nomor WhatsApp</label>
                        <input type="text" id="nomorWhatsapp" readonly>
                    </div>

                    <div class="input-group">
                        <label>Tanggal Maintenance</label>
                        <input type="text" id="tanggalMaintenance" readonly>
                    </div>

                    <div class="input-group">
                        <label>Prioritas</label>
                        <input type="text" id="prioritasJadwal" readonly>
                    </div>

                    <div class="input-group full">
                        <label>Rekomendasi / Tindakan Maintenance</label>
                        <textarea id="rekomendasiJadwal" readonly></textarea>
                    </div>

                    <!-- Status will be set automatically to 'Selesai' on save -->

                    <div class="input-group full">
                        <label>Hasil Pengecekan</label>
                        <textarea id="hasilPengecekan" placeholder="Jelaskan hasil pengecekan dan kondisi mesin setelah pekerjaan"></textarea>
                    </div>

                    <div class="input-group full">
                        <label>Tindakan Yang Dilakukan</label>
                        <textarea id="tindakanDilakukan" placeholder="Contoh: Penggantian bearing, pelumasan"></textarea>
                    </div>

                    <div class="input-group">
    <label>Durasi Pekerjaan (Jam)</label>
    <input
        type="number"
        id="durasiJam"
        step="0.1"
        min="0"
        placeholder="Contoh: 2.5"
    >
</div>

<div class="input-group">
    <label>Biaya Maintenance (Rp)</label>
    <input
        type="number"
        id="biayaMaintenance"
        min="0"
        placeholder="Contoh: 250000"
    >
</div>

<div class="input-group full">
    <label>Upload Dokumentasi Maintenance</label>

    <input
        type="file"
        id="dokumentasiFile"
        class="form-control"
        accept="image/*,.pdf"
    >
    <div id="fileName">Belum ada file dipilih</div>

</div>

                </div>

                <div class="button-area">
                    <button type="reset" class="btn-cancel" onclick="resetForm()">Batal</button>
                    <button type="button" class="btn-submit" onclick="submitPekerjaan()">Simpan Data</button>
                </div>
            </form>
        </div>

        <script>
            let jadwalList = [];

            function formatTanggal(date) {
                if (!date) return '-';
                const d = new Date(date);
                if (isNaN(d)) return '-';
                return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
            }

            function loadJadwalForTechnician() {
                fetch('/api/jadwal-maintenance?per_page=200')
                    .then(r => r.json())
                    .then(res => {
                        if (!res.success) return showToast('Gagal memuat jadwal', 'error');
                        jadwalList = res.data.data || res.data || [];
                        const sel = document.getElementById('selectJadwal');
                        sel.innerHTML = '<option value="">Pilih Jadwal yang Akan Dikerjakan</option>' + jadwalList.filter(j => {
                            const s = (j.status_jadwal || j.status || '').toString();
                            return ['Dijadwalkan'].includes(s);
                        }).map(j => `
                            <option value="${j.id}">${j.nama_aset || j.kode_aset || j.kode_mesin} — ${j.nama_komponen || j.kode_komponen || j.komponen || ''} — ${formatTanggal(j.tanggal_maintenance)}</option>
                        `).join('');

                        const params = new URLSearchParams(window.location.search);
                        const selectedJadwalId = params.get('jadwal_id');
                        if (selectedJadwalId) {
                            const option = sel.querySelector(`option[value="${selectedJadwalId}"]`);
                            if (option) {
                                sel.value = selectedJadwalId;
                                sel.dispatchEvent(new Event('change'));
                            }
                        }
                    })
                    .catch(() => showToast('Error memuat jadwal', 'error'));
            }

            // register select change handler after DOM is ready to avoid null element errors

            function resetForm() {
                document.getElementById('formPekerjaan').reset();
                document.getElementById('namaAset').value = '';
                document.getElementById('namaKomponen').value = '';
                document.getElementById('penanggungJawab').value = '';
                document.getElementById('nomorWhatsapp').value = '';
                document.getElementById('tanggalMaintenance').value = '';
                document.getElementById('prioritasJadwal').value = '';
                document.getElementById('rekomendasiJadwal').value = '';
                document.getElementById('fileName').innerText = 'Belum ada file dipilih';
            }

            function submitPekerjaan() {
                const jadwalId = document.getElementById('selectJadwal').value;
                if (!jadwalId) return showToast('Pilih jadwal yang akan dikerjakan terlebih dahulu.', 'warning');

                const status = 'Selesai';

                const formData = new FormData();
                formData.append('jadwal_maintenance_id', jadwalId);
                formData.append('tanggal_pelaksanaan', new Date().toISOString().slice(0,10));
                formData.append('penanggungjawab', document.getElementById('penanggungJawab').value);
                formData.append('nomor_whatsapp', document.getElementById('nomorWhatsapp').value);
                // teknisi akan diisi di server dari user yang login jika tersedia
                formData.append('status_pekerjaan', status);
                formData.append('hasil_pengecekan', document.getElementById('hasilPengecekan').value);
                formData.append('tindakan_dilakukan', document.getElementById('tindakanDilakukan').value);
                formData.append('durasi_pekerjaan', document.getElementById('durasiJam').value);
                formData.append('biaya_maintenance', document.getElementById('biayaMaintenance').value);
                const file = document.getElementById('dokumentasiFile').files[0];
                if (file) formData.append('dokumentasi', file);

                fetch('/api/riwayat-pekerjaan', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        showToast(res.message || 'Data maintenance berhasil disimpan.', 'success');
                        showToast('Status jadwal berhasil diperbarui.', 'success');
                        // refresh jadwal list and reset form
                        loadJadwalForTechnician();
                        resetForm();
                        // redirect to riwayat pekerjaan
                        setTimeout(() => { window.location.href = '/riwayatpekerjaan'; }, 800);
                    } else {
                        showToast(res.message || 'Gagal menyimpan data maintenance.', 'error');
                    }
                })
                .catch(() => showToast('Gagal menyimpan data maintenance.', 'error'));
            }

            document.addEventListener('DOMContentLoaded', function() {
                loadJadwalForTechnician();
                const sel = document.getElementById('selectJadwal');
                if (!sel) return;
                sel.addEventListener('change', function() {
                    const id = this.value;
                    const jadwal = jadwalList.find(j => String(j.id) === String(id));
                    if (!jadwal) {
                        document.getElementById('namaAset').value = '';
                        document.getElementById('namaKomponen').value = '';
                        document.getElementById('tanggalMaintenance').value = '';
                        document.getElementById('prioritasJadwal').value = '';
                        document.getElementById('rekomendasiJadwal').value = '';
                        return;
                    }

                    document.getElementById('namaAset').value = jadwal.nama_aset || jadwal.kode_aset || jadwal.kode_mesin || '-';
                    document.getElementById('namaKomponen').value = jadwal.nama_komponen || jadwal.kode_komponen || jadwal.komponen || '-';
                    document.getElementById('penanggungJawab').value = jadwal.penanggungjawab || '-';
                    document.getElementById('nomorWhatsapp').value = jadwal.nomor_whatsapp || '-';
                    document.getElementById('tanggalMaintenance').value = formatTanggal(jadwal.tanggal_maintenance);
                    document.getElementById('prioritasJadwal').value = jadwal.prioritas || jadwal.kategori_risiko || '-';
                    document.getElementById('rekomendasiJadwal').value = jadwal.tindakan_maintenance || jadwal.rekomendasi_perawatan || '';
                });
            });

            document.getElementById('dokumentasiFile')
            .addEventListener('change', function(){

            document.getElementById('fileName').innerText =
                this.files[0]
                ? this.files[0].name
                : 'Belum ada file dipilih';
            });
        </script>
@endsection
