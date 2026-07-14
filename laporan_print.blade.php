<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['title'] ?? 'Laporan' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 12mm 14mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell {
            width: 90px;
            text-align: center;
        }

        .logo-cell img {
            width: 72px;
            height: auto;
        }

        .kop-text {
            text-align: center;
            line-height: 1.25;
        }

        .kop-text h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .kop-text p {
            margin: 3px 0 0 0;
            font-size: 10.5px;
        }

        .report-title {
            text-align: center;
            margin: 16px 0 10px 0;
        }

        .report-title h3 {
            margin: 0;
            font-size: 15px;
            text-transform: uppercase;
            text-decoration: underline;
            color: #111827;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }

        .meta-table td {
            border: none;
            padding: 2px 0;
            font-size: 10.5px;
        }

        .meta-label {
            width: 90px;
            font-weight: bold;
        }

        .summary {
            margin: 8px 0 12px 0;
            padding: 8px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            font-size: 10.5px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-inside: auto;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .data-table th {
            background: #1d4ed8;
            color: #ffffff;
            border: 1px solid #1e3a8a;
            padding: 6px 5px;
            font-size: 9.5px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .data-table td {
            border: 1px solid #d1d5db;
            padding: 6px 5px;
            font-size: 9.5px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f3f4f6;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 14px;
            font-size: 9.5px;
            color: #374151;
            text-align: center;
            border-top: 1px solid #d1d5db;
            padding-top: 6px;
        }

        .no-data {
            text-align: center;
            padding: 12px;
            font-style: italic;
        }
    </style>
</head>
<body>
@php
    $logo = $logoPath ?? public_path('assets/logo_perumda.png');
    $judul = strtoupper($report['title'] ?? 'LAPORAN SISTEM PREVENTIVE MAINTENANCE');
    $rows = $report['rows'] ?? [];
    $columns = $report['columns'] ?? [];
@endphp

<div class="kop-surat">
    <table class="kop-table">
        <tr>
            <td class="logo-cell">
                @if(file_exists($logo))
                    <img src="{{ $logo }}" alt="Logo Tirta Musi">
                @endif
            </td>
            <td class="kop-text">
                <h1>PERUSAHAAN UMUM DAERAH</h1>
                <h2>TIRTA MUSI PALEMBANG</h2>
                <p>Jl. Rambutan Ujung No. 01, 30 Ilir, Ilir Barat II, 30144</p>
                <p>Telp. 0711 355089 - Website: www.perumdatirtamusi.co.id</p>
            </td>
            <td style="width:90px;"></td>
        </tr>
    </table>
</div>

<div class="report-title">
    <h3>{{ $judul }}</h3>
</div>

<table class="meta-table">
    <tr>
        <td class="meta-label">Periode</td>
        <td>: {{ $periode[0] ?? '-' }} s/d {{ $periode[1] ?? '-' }}</td>
    </tr>
    <tr>
        <td class="meta-label">Tanggal Cetak</td>
        <td>: {{ date('d-m-Y H:i') }}</td>
    </tr>
    <tr>
        <td class="meta-label">Jumlah Data</td>
        <td>: {{ count($rows) }} data</td>
    </tr>
</table>

<!-- <div class="summary">
    <strong>Ringkasan:</strong> Laporan ini menampilkan data {{ strtolower($report['title'] ?? 'preventive maintenance') }} berdasarkan periode yang dipilih pada sistem.
</div> -->

<table class="data-table">
    <thead>
        <tr>
            @foreach($columns as $col)
                <th>{{ $col }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($columns) ?: 1 }}" class="no-data">Tidak ada data laporan pada periode yang dipilih.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Dihasilkan oleh Sistem Preventive Maintenance Perumda Tirta Musi Palembang
</div>
</body>
</html>
