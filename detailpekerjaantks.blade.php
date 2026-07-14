@extends('layouts.app')
@section('title', 'Detail Pekerjaan')
@php
    $activePage = 'riwayatpekerjaan';
    $sidebarType = 'teknisi';
    $riwayatId = $riwayatId ?? null;
@endphp
@section('content')
<div class="topbar">
    <h1>Detail Pekerjaan Teknisi</h1>
    <p>Informasi lengkap pekerjaan maintenance berdasarkan riwayat yang tersimpan.</p>
</div>

<div class="detail-card">
    <h2>Informasi Aset</h2>
    <div class="detail-grid">
        <div class="detail-item">
            <span>Nama Aset</span>
            <strong id="detailNamaAset">-</strong>
        </div>
        <div class="detail-item">
            <span>Nama Komponen</span>
            <strong id="detailNamaKomponen">-</strong>
        </div>
        <div class="detail-item">
            <span>Tanggal Pelaksanaan</span>
            <strong id="detailTanggalPelaksanaan">-</strong>
        </div>
        <div class="detail-item">
            <span>Status Pekerjaan</span>
            <strong id="detailStatus" class="status">-</strong>
        </div>
    </div>
</div>

<div class="detail-card">
    <h2>Detail Pekerjaan</h2>
    <div class="detail-grid">
        <div class="detail-item full">
            <span>Hasil Pengecekan</span>
            <strong id="detailHasilPengecekan">-</strong>
        </div>
        <div class="detail-item full">
            <span>Tindakan Yang Dilakukan</span>
            <strong id="detailTindakan">-</strong>
        </div>
        <div class="detail-item">
            <span>Durasi Pekerjaan</span>
            <strong id="detailDurasi">-</strong>
        </div>
        <div class="detail-item">
            <span>Biaya Maintenance</span>
            <strong id="detailBiaya">-</strong>
        </div>
        <div class="detail-item full">
            <span>Dokumentasi</span>
            <strong id="detailDokumentasi">-</strong>
        </div>
    </div>
</div>

<div class="button-area">
    <a href="/riwayatpekerjaan" class="btn-back">Kembali</a>
    <a href="#" class="btn-print">Cetak Detail</a>
</div>

<script>
    const riwayatId = '{{ $riwayatId }}';

    function formatTanggal(date) {
        if (!date) return '-';
        const d = new Date(date);
        if (isNaN(d)) return '-';
        return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function formatRupiah(value) {
        if (value === null || value === undefined || value === '') return '-';
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
    }

    function loadDetail() {
        if (!riwayatId) {
            showToast('Data detail tidak ditemukan.', 'error');
            return;
        }

        fetch(`/api/riwayat-pekerjaan/${riwayatId}`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    showToast('Gagal memuat detail riwayat.', 'error');
                    return;
                }

                const item = res.data;
                document.getElementById('detailNamaAset').textContent = item.jadwal?.nama_aset || item.kode_aset || '-';
                document.getElementById('detailNamaKomponen').textContent = item.jadwal?.nama_komponen || item.kode_komponen || '-';
                document.getElementById('detailTanggalPelaksanaan').textContent = formatTanggal(item.tanggal_pekerjaan);
                document.getElementById('detailStatus').textContent = item.status || '-';
                document.getElementById('detailHasilPengecekan').textContent = item.hasil_pengecekan || item.hasil_pekerjaan || '-';
                document.getElementById('detailTindakan').textContent = item.tindakan || item.catatan_teknisi || '-';
                document.getElementById('detailDurasi').textContent = item.durasi_jam ? item.durasi_jam + ' Jam' : '-';
                document.getElementById('detailBiaya').textContent = formatRupiah(item.biaya);
                const dokumentasiField = document.getElementById('detailDokumentasi');
                if (item.dokumentasi) {
                    dokumentasiField.innerHTML = `<a href="/storage/${item.dokumentasi}" target="_blank">Lihat Dokumentasi</a>`;
                } else {
                    dokumentasiField.textContent = '-';
                }
            })
            .catch(() => showToast('Gagal memuat detail riwayat.', 'error'));
    }

    document.addEventListener('DOMContentLoaded', loadDetail);
</script>
@endsection
