@extends('layouts.app')
@section('title', 'Data Komponen')
@php
    $activePage = 'datakomponen';
    $sidebarType = 'admin';
@endphp
@section('content')
<!-- <div class="topbar">
                <h1>Data Komponen Mesin</h1>
                <p>Halaman ini digunakan admin untuk mengelola komponen kritis dari setiap aset mesin.</p>
            </div> -->

            <div class="content-card">
                <div class="action-bar">
                    <input type="text" class="search-input" placeholder="Cari komponen / kode mesin...">
                    <a href="#" class="btn-add">+ Tambah Komponen</a>
                </div>

                <div class="table-responsive">
                    <table>
                        <tr>
                            <th>No</th>
                            <th>Kode Mesin</th>
                            <th>Nama Komponen</th>
                            <th>Fungsi Komponen</th>
                            <th>Umur / Jam Operasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>

                        <tr>
                            <td>1</td>
                            <td>CP 2</td>
                            <td>Motor Listrik</td>
                            <td>Menggerakkan impeller pompa distribusi</td>
                            <td>9.294 Jam</td>
                            <td><span class="status kritis">Kritis</span></td>
                            <td>
                                <a href="#" class="btn-detail">Detail</a>
                                <a href="#" class="btn-edit">Edit</a>
                                <a href="#" class="btn-delete">Hapus</a>
                            </td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>CP 2</td>
                            <td>Bearing</td>
                            <td>Menumpu dan menstabilkan poros pompa</td>
                            <td>9.294 Jam</td>
                            <td><span class="status kritis">Kritis</span></td>
                            <td>
                                <a href="#" class="btn-detail">Detail</a>
                                <a href="#" class="btn-edit">Edit</a>
                                <a href="#" class="btn-delete">Hapus</a>
                            </td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td>CP 1</td>
                            <td>Mechanical Seal</td>
                            <td>Mencegah kebocoran air pada poros pompa</td>
                            <td>5.057 Jam</td>
                            <td><span class="status perlu-monitoring">Perlu Monitoring</span></td>
                            <td>
                                <a href="#" class="btn-detail">Detail</a>
                                <a href="#" class="btn-edit">Edit</a>
                                <a href="#" class="btn-delete">Hapus</a>
                            </td>
                        </tr>

                        <tr>
                            <td>4</td>
                            <td>CP 3</td>
                            <td>Impeller</td>
                            <td>Membantu proses pemompaan air</td>
                            <td>5.548 Jam</td>
                            <td><span class="status baik">Baik</span></td>
                            <td>
                                <a href="#" class="btn-detail">Detail</a>
                                <a href="#" class="btn-edit">Edit</a>
                                <a href="#" class="btn-delete">Hapus</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
@endsection
