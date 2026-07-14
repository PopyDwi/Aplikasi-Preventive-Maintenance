@extends('layouts.app')
@section('title', 'Downtime Mesin')
@php
    $activePage = 'downtime';
    $sidebarType = 'admin';
@endphp
@section('content')
<!-- Downtime Dashboard -->

        <div class="summary-card">
            <h2>Ringkasan Downtime</h2>
            <div class="summary-grid">
                <div class="summary-box">
                    <h3>Total Downtime (Jam)</h3>
                    <h1 id="totalDowntime">145</h1>
                </div>
                <div class="summary-box">
                    <h3>Mesin Critical</h3>
                    <h1 id="mesinCritical">4</h1>
                </div>
                <div class="summary-box">
                    <h3>Frekuensi Kerusakan</h3>
                    <h1 id="frekuensi">18</h1>
                </div>
                <div class="summary-box">
                    <h3>Maintenance Bulan Ini</h3>
                    <h1 id="maintenanceBulan">12</h1>
                </div>
            </div>
        </div>

        <div class="chart-grid" style="margin-top:10px;">
            <div class="chart-card">
                <h3>Grafik Downtime per Bulan</h3>
                <canvas id="chartMonthly"></canvas>
            </div>
            <div class="chart-card">
                <h3>Grafik Downtime per Mesin</h3>
                <canvas id="chartByMachine"></canvas>
            </div>
        </div>

        <div class="top-machines" style="margin-top:18px;">
            <h2>Top Mesin dengan Downtime Tertinggi</h2>
            <div class="table-card">
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th>No</th>
                            <th>Kode Mesin</th>
                            <th>Nama Mesin</th>
                            <th>Total Downtime (Jam)</th>
                            <th>Frekuensi Kerusakan</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>CP 2</td>
                            <td>Pompa Sentral</td>
                            <td>48</td>
                            <td>5</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>CP 5</td>
                            <td>Motor Listrik</td>
                            <td>30</td>
                            <td>4</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>CP 1</td>
                            <td>Seal Unit</td>
                            <td>12</td>
                            <td>2</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>WP 1</td>
                            <td>Impeller</td>
                            <td>5</td>
                            <td>1</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Filters and search -->
        <div class="table-card" style="margin-top:18px;">
            <h2>Riwayat Downtime Mesin</h2>

            <div class="action-bar">
                <div class="filters">
                    <select id="filterMonth" class="input-group">
                        <option value="">Semua Bulan</option>
                        <option value="1">Jan</option>
                        <option value="2">Feb</option>
                        <option value="3">Mar</option>
                        <option value="4">Apr</option>
                        <option value="5">Mei</option>
                        <option value="6">Jun</option>
                        <option value="7">Jul</option>
                        <option value="8">Agu</option>
                        <option value="9">Sep</option>
                        <option value="10">Okt</option>
                        <option value="11">Nov</option>
                        <option value="12">Des</option>
                    </select>

                    <select id="filterYear" class="input-group">
                        <option value="">Semua Tahun</option>
                        <option>2024</option>
                        <option>2025</option>
                        <option selected>2026</option>
                    </select>

                    <select id="filterStatus" class="input-group">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="proses">Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>

                    <select id="filterMachine" class="input-group">
                        <option value="">Semua Mesin</option>
                        <option>CP 1</option>
                        <option>CP 2</option>
                        <option>CP 5</option>
                        <option>WP 1</option>
                    </select>
                </div>

                <div style="min-width:260px;">
                    <input type="text" id="searchInput" class="search-input" placeholder="Cari kode/komponen/teknisi...">
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table id="downtimeTable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Kerusakan</th>
                            <th>Kode Mesin</th>
                            <th>Nama Mesin</th>
                            <th>Komponen</th>
                            <th>Penyebab Kerusakan</th>
                            <th>Durasi Downtime (Jam)</th>
                            <th>Teknisi</th>
                            <th>Status Perbaikan</th>
                            <th>Kategori Risiko</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>2026-05-13</td>
                            <td>CP 2</td>
                            <td>Pompa Sentral</td>
                            <td>Motor Listrik</td>
                            <td>Getaran Abnormal</td>
                            <td>48</td>
                            <td>Teknisi 1</td>
                            <td><span class="status menunggu">Menunggu Validasi</span></td>
                            <td><span class="status tinggi">Tinggi</span></td>
                            <td>
                                <a href="#" class="btn-detail">Detail</a>
                                <a href="#" class="btn-validasi">Validasi</a>
                            </td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>2026-05-12</td>
                            <td>CP 5</td>
                            <td>Motor Listrik</td>
                            <td>Bearing</td>
                            <td>Keausan</td>
                            <td>30</td>
                            <td>Teknisi 2</td>
                            <td><span class="status proses">Diproses</span></td>
                        <td><span class="status sedang">Sedang</span></td>
                        <td>
                            <a href="#" class="btn-detail">Detail</a>
                            <a href="#" class="btn-validasi">Validasi</a>
                        </td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td>2026-05-11</td>
                        <td>CP 1</td>
                        <td>Seal Unit</td>
                        <td>Mechanical Seal</td>
                        <td>Kebocoran</td>
                        <td>12</td>
                        <td>Teknisi 1</td>
                        <td><span class="status selesai">Selesai</span></td>
                        <td><span class="status rendah">Rendah</span></td>
                        <td>
                            <a href="#" class="btn-detail">Detail</a>
                            <a href="#" class="btn-validasi">Validasi</a>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sample data for charts
        const monthlyLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const monthlyData = [20,15,18,22,30,25,28,20,18,24,26,14];

        const ctx = document.getElementById('chartMonthly');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Downtime (Jam)',
                        data: monthlyData,
                        backgroundColor: 'rgba(59,130,246,0.8)'
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        const ctx2 = document.getElementById('chartByMachine');
        if (ctx2) {
            new Chart(ctx2.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['CP 2','CP 5','CP 1','WP 1'],
                    datasets: [{ data: [48,30,12,5], backgroundColor: ['#3b82f6','#60a5fa','#93c5fd','#bfdbfe'] }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // Simple client-side table filtering
        function matchesFilter(row, q, month, year, status, machine) {
            const text = row.textContent.toLowerCase();
            if (q && !text.includes(q)) return false;
            if (month) {
                const date = row.cells[1].textContent.trim();
                if (!date) return false;
                const m = new Date(date).getMonth()+1;
                if (String(m) !== month) return false;
            }
            if (year) {
                const date = row.cells[1].textContent.trim();
                const y = new Date(date).getFullYear();
                if (String(y) !== year) return false;
            }
            if (status) {
                const s = row.querySelector('td:nth-child(9) > .status');
                if (!s || !s.className.includes(status)) return false;
            }
            if (machine) {
                const code = row.cells[2].textContent.trim();
                if (!code.includes(machine)) return false;
            }
            return true;
        }

        function applyFilters() {
            const q = document.getElementById('searchInput').value.trim().toLowerCase();
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;
            const status = document.getElementById('filterStatus').value;
            const machine = document.getElementById('filterMachine').value;
            const tbody = document.querySelector('#downtimeTable tbody');
            Array.from(tbody.rows).forEach(row => {
                row.style.display = matchesFilter(row,q,month,year,status,machine) ? '' : 'none';
            });
        }

        document.getElementById('searchInput').addEventListener('input', applyFilters);
        ['filterMonth','filterYear','filterStatus','filterMachine'].forEach(id=>{
            document.getElementById(id).addEventListener('change', applyFilters);
        });
    </script>
@endpush
