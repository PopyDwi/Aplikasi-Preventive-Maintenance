@extends('layouts.app')
@section('title', 'Dashboard Teknisi')
@php
    $activePage = 'dashboardteknisi';
    $sidebarType = 'teknisi';
@endphp
@section('content')
            <!-- <div class="topbar">
                <div>
                    <h1>Dashboard Teknisi</h1>
                    <p>Kelola pekerjaan preventive maintenance dan lihat status tugas teknisi secara real-time.</p>
                </div>

                <div class="profile">
                    Teknisi
                </div>
            </div> -->

            <div class="cards">
                <div class="card card-blue card-machines">
                    <div class="card-header">
                        <div>
                            <p class="card-label">Total Jadwal Maintenance</p>
                            <h1 class="card-value">{{ $totalJadwal }}</h1>
                        </div>
                        <span class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M4 6h16v4H4V6zm0 8h16v6H4v-6zm4-6v6h2V8H8zm4 0v6h2V8h-2z"/></svg>
                        </span>
                    </div>
                </div>

                <div class="card card-yellow card-pending">
                    <div class="card-header">
                        <div>
                            <p class="card-label">Dijadwalkan</p>
                            <h1 class="card-value">{{ $belum }}</h1>
                        </div>
                        <span class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm1 15l-5-5 1.41-1.41L13 14.17l5.59-5.59L20 10z"/></svg>
                        </span>
                    </div>
                </div>

                <div class="card card-green card-success">
                    <div class="card-header">
                        <div>
                            <p class="card-label">Selesai</p>
                            <h1 class="card-value">{{ $selesai }}</h1>
                        </div>
                        <span class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M9 16.17l-3.88-3.88L4 13.41 9 18.41 20 7.41 18.59 6 9 16.17z"/></svg>
                        </span>
                    </div>
                </div>

                <div class="card card-indigo card-next">
                    <div class="card-header">
                        <div>
                            <p class="card-label">Jadwal Terdekat</p>
                            <h1 class="card-value">{{ optional($jadwalTerdekat)->tanggal_maintenance?->format('d M') ?? '-' }}</h1>
                        </div>
                        <span class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 3a9 9 0 1 0 9 9 9.01 9.01 0 0 0-9-9zm1 13h-2V7h2z"/></svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Daftar Jadwal Maintenance</h2>

                <div class="table-responsive">
                    <table>
                        <thead style="background:#0f172a;color:#ffffff;">
                            <tr>
                                <th>No</th>
                                <th>Nama Aset</th>
                                <th>Nama Komponen</th>
                                <th>Tanggal Maintenance</th>
                                <th>Prioritas</th>
                                <th>Status Jadwal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($daftarJadwal as $index => $jadwal)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $jadwal->nama_aset }}</td>
                                    <td>{{ $jadwal->nama_komponen }}</td>
                                    <td>{{ optional($jadwal->tanggal_maintenance)->translatedFormat('d F Y') }}</td>
                                    <td>{{ $jadwal->prioritas ?? '-' }}</td>
                                    <td>
                                        @if($jadwal->status_jadwal === 'Selesai')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border bg-emerald-50 text-emerald-700 border-emerald-200">{{ $jadwal->status_jadwal }}</span>
                                        @elseif($jadwal->status_jadwal === 'Dijadwalkan')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border bg-amber-50 text-amber-700 border-amber-200">{{ $jadwal->status_jadwal }}</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border bg-slate-50 text-slate-700 border-slate-200">{{ $jadwal->status_jadwal }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="/inputmaintenance?jadwal_id={{ $jadwal->id }}" class="btn-detail">Kerjakan</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Tidak ada jadwal maintenance aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
@endsection
