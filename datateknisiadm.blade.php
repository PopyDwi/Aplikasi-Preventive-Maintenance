@extends('layouts.app')
@section('title', 'Data Teknisi')
@php
    $activePage = 'datateknisi';
    $sidebarType = 'admin';
@endphp
@section('content')
<!-- <div class="topbar">
                <h1>Data Teknisi</h1>
                <p>Halaman ini digunakan admin untuk mengelola akun teknisi yang dapat mengakses sistem.</p>
            </div> -->

            <div class="form-card">
                <h2>Tambah / Edit Akun Teknisi</h2>

                <form id="teknisiForm">
                    <input type="hidden" id="teknisiId" value="">
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Username</label>
                            <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                        </div>

                        <div class="input-group">
                            <label>Nomor WhatsApp</label>
                            <input type="text" id="nomorWhatsappTeknisi" name="nomor_whatsapp" placeholder="Contoh: 6281234567890">
                        </div>

                        <div class="input-group">
                            <label>Password</label>
                            <input type="password" id="password" name="password" placeholder="Masukkan password (kosongkan jika tidak ingin mengubah)">
                        </div>
                    </div>

                    <div class="button-area">
                        <button type="reset" id="btnReset" class="btn-reset">Batal</button>
                        <button type="submit" id="btnSubmit" class="btn-submit">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="table-card">
                <h2>Daftar Akun Teknisi</h2>

                <div class="action-bar">
                    <input type="text" id="searchInput" class="search-input" placeholder="Cari username teknisi...">
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>WhatsApp</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="teknisiTable">
                            <!-- rows injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                async function loadTeknisi(q = ''){
                    const resp = await fetch('/api/datateknisi' + (q? '?q=' + encodeURIComponent(q) : ''));
                    const json = await resp.json();
                    const tbody = document.getElementById('teknisiTable');
                    tbody.innerHTML = '';
                    json.data.forEach((u, idx) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${idx + 1}</td>
                            <td>${escapeHtml(u.username)}</td>
                            <td>${escapeHtml(u.role)}</td>
                            <td>${escapeHtml(u.nomor_whatsapp || '-')}</td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn-edit btn-reset" data-id="${u.id}">Edit</button>
                                    <button type="button" class="btn-delete btn-delete" data-id="${u.id}">Hapus</button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });

                    // attach handlers
                    document.querySelectorAll('.btn-edit').forEach(b => b.addEventListener('click', onEdit));
                    document.querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', onDelete));
                }

                function escapeHtml(text){
                    if(!text) return '';
                    return text.replace(/[&<>"']/g, function(m){
                        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#039;"}[m];
                    });
                }

                async function onEdit(e){
                    const id = e.currentTarget.dataset.id;
                    const resp = await fetch('/api/datateknisi');
                    const json = await resp.json();
                    const user = json.data.find(x => x.id == id);
                    if(!user) return alert('Data teknisi tidak ditemukan');
                    document.getElementById('teknisiId').value = user.id;
                    document.getElementById('username').value = user.username || '';
                    document.getElementById('password').value = '';
                    document.getElementById('nomorWhatsappTeknisi').value = user.nomor_whatsapp || '';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                async function onDelete(e){
                    if(!confirm('Hapus akun teknisi ini?')) return;
                    const id = e.currentTarget.dataset.id;
                    const resp = await fetch('/api/datateknisi/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });
                    if(resp.ok){
                        alert('Akun teknisi berhasil dihapus.');
                        loadTeknisi(document.getElementById('searchInput').value.trim());
                    } else {
                        alert('Gagal menghapus akun teknisi.');
                    }
                }

                document.getElementById('searchInput').addEventListener('input', function(e){
                    const q = e.target.value.trim();
                    loadTeknisi(q);
                });

                document.getElementById('teknisiForm').addEventListener('submit', async function(e){
                    e.preventDefault();
                    const id = document.getElementById('teknisiId').value;
                    const payload = {
                        username: document.getElementById('username').value.trim(),
                        password: document.getElementById('password').value,
                        nomor_whatsapp: document.getElementById('nomorWhatsappTeknisi').value.trim(),
                    };

                    let url = '/api/datateknisi';
                    let method = 'POST';
                    if(id){
                        url = '/api/datateknisi/' + id;
                        method = 'PUT';
                    }

                    const resp = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    if(resp.status === 422){
                        const json = await resp.json();
                        const errs = json.errors || {};
                        const first = Object.values(errs)[0][0] || 'Validasi gagal.';
                        alert(first);
                        return;
                    }

                    if(resp.ok){
                        const json = await resp.json();
                        alert(json.message || 'Sukses.');
                        document.getElementById('teknisiForm').reset();
                        document.getElementById('teknisiId').value = '';
                        loadTeknisi(document.getElementById('searchInput').value.trim());
                    } else {
                        alert('Terjadi kesalahan saat menyimpan.');
                    }
                });

                document.getElementById('btnReset').addEventListener('click', function(){
                    document.getElementById('teknisiId').value = '';
                });

                // initial load
                loadTeknisi();
            </script>
@endsection
