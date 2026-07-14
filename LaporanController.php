<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aset;
use App\Models\Kerusakan;
use App\Models\FmeaAnalisis;
use App\Models\JadwalMaintenance;
use App\Models\EstimasiBiaya;
use App\Models\RiwayatPekerjaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class LaporanController extends Controller
{
    public function view()
    {
        return view('laporanadm');
    }

    public function api(Request $request)
    {
        $jenis = $request->query('jenis');
        $tanggalAwal = $request->query('tanggal_awal') ?: $request->query('from');
        $tanggalAkhir = $request->query('tanggal_akhir') ?: $request->query('to');

        $start = $tanggalAwal ? date('Y-m-d', strtotime($tanggalAwal)) : null;
        $end = $tanggalAkhir ? date('Y-m-d', strtotime($tanggalAkhir)) : null;

        $result = [
            'title' => 'Laporan',
            'description' => '',
            'columns' => [],
            'rows' => [],
            'summary' => null,
        ];

        switch ($jenis) {
            case 'data-aset':
                $result['title'] = 'Laporan Data Aset';
                $result['description'] = 'Daftar aset.';
                $result['columns'] = ['No','Kode Aset','Nama Aset','Status','Tanggal Instalasi'];
                $q = Aset::query();
                if ($start) $q->where('tanggal_instalasi','>=',$start);
                if ($end) $q->where('tanggal_instalasi','<=',$end);
                $rows = $q->orderBy('nama_aset')->get();
                foreach ($rows as $i=>$r) {
                    $result['rows'][] = [$i+1,$r->kode_aset,$r->nama_aset,$r->status,$r->tanggal_instalasi?->format('Y-m-d')];
                }
                $result['summary'] = ['total_aset' => $rows->count()];
                $statusCounts = $rows->groupBy('status')->map->count();
                $result['chart'] = [
                    'type' => 'bar',
                    'labels' => $statusCounts->keys()->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Aset',
                        'data' => $statusCounts->values()->toArray(),
                        'backgroundColor' => ['#3b82f6','#10b981','#ef4444','#f59e0b'],
                    ]],
                ];
                break;

            case 'kerusakan':
                $result['title'] = 'Laporan Data Kerusakan';
                $result['description'] = 'Riwayat kerusakan.';
                $result['columns'] = ['No','Tanggal Kerusakan','Nama Aset','Komponen','Jenis Kerusakan','Downtime','Estimasi Biaya','Status'];
                $q = Kerusakan::query();
                if ($start) $q->where('tanggal_kerusakan','>=',$start);
                if ($end) $q->where('tanggal_kerusakan','<=',$end);
                $rows = $q->orderBy('tanggal_kerusakan','desc')->get();
                foreach ($rows as $i=>$r) {
                    $result['rows'][] = [$i+1,$r->tanggal_kerusakan?->format('Y-m-d'),$r->nama_aset,$r->nama_komponen,$r->jenis_kerusakan,$r->downtime_jam,$r->estimasi_biaya,$r->status];
                }
                $result['summary'] = ['total_kerusakan' => $rows->count()];
                $statusCounts = $rows->groupBy('status')->map->count();
                $result['chart'] = [
                    'type' => 'bar',
                    'labels' => $statusCounts->keys()->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Kerusakan',
                        'data' => $statusCounts->values()->toArray(),
                        'backgroundColor' => ['#ef4444','#f59e0b','#10b981','#3b82f6'],
                    ]],
                ];
                break;

            case 'analisis-rcm':
                $result['title'] = 'Laporan Analisis Risiko Kerusakan';
                $result['description'] = 'Hasil analisis risiko kerusakan yang dihasilkan otomatis dari input teknisi.';
                $result['columns'] = ['No','Nama Aset','Komponen','Mode Kegagalan','S','O','D','RPN','Kategori Risiko','Rekomendasi'];
                $rows = FmeaAnalisis::orderBy('rpn','desc')->get();
                foreach ($rows as $i=>$r) {
                    $result['rows'][] = [$i+1,$r->nama_aset,$r->nama_komponen,$r->mode_kegagalan,$r->severity,$r->occurrence,$r->detection,$r->rpn,$r->kategori_risiko,$r->rekomendasi_perawatan];
                }
                $result['summary'] = ['total_analisis' => $rows->count()];
                $riskCounts = $rows->groupBy('kategori_risiko')->map->count();
                $result['chart'] = [
                    'type' => 'pie',
                    'labels' => $riskCounts->keys()->toArray(),
                    'datasets' => [[
                        'label' => 'Kategori Risiko',
                        'data' => $riskCounts->values()->toArray(),
                        'backgroundColor' => ['#f59e0b','#ef4444','#10b981','#3b82f6'],
                    ]],
                ];
                break;

            case 'jadwal-maintenance':
            case 'maintenance':
                $result['title'] = 'Laporan Jadwal Maintenance';
                $result['description'] = 'Jadwal maintenance.';
                $result['columns'] = ['No','Nama Aset','Komponen','Tanggal Maintenance','Prioritas','Status Jadwal','Tindakan Maintenance'];
                $q = JadwalMaintenance::query();
                if ($start) $q->where('tanggal_maintenance','>=',$start);
                if ($end) $q->where('tanggal_maintenance','<=',$end);
                $rows = $q->orderBy('tanggal_maintenance','desc')->get();
                foreach ($rows as $i=>$r) {
                    $result['rows'][] = [$i+1,$r->nama_aset,$r->nama_komponen,$r->tanggal_maintenance?->format('Y-m-d'),$r->prioritas,$r->status,strip_tags($r->tindakan_maintenance)];
                }
                $result['summary'] = ['total_jadwal' => $rows->count()];
                $statusCounts = $rows->groupBy('status')->map->count();
                $result['chart'] = [
                    'type' => 'bar',
                    'labels' => $statusCounts->keys()->toArray(),
                    'datasets' => [[
                        'label' => 'Jumlah Jadwal',
                        'data' => $statusCounts->values()->toArray(),
                        'backgroundColor' => ['#3b82f6','#10b981','#ef4444','#f59e0b'],
                    ]],
                ];
                break;

            case 'estimasi-biaya':
                $result['title'] = 'Laporan Estimasi Biaya';
                $result['description'] = 'Estimasi biaya perawatan.';
                $result['columns'] = ['No','Tanggal','Nama Aset','Komponen','Total Downtime','Biaya per Jam','Biaya Perbaikan','Total Estimasi'];
                $q = EstimasiBiaya::query();
                if ($start) $q->where('tanggal','>=',$start);
                if ($end) $q->where('tanggal','<=',$end);
                $rows = $q->orderBy('tanggal','desc')->get();
                foreach ($rows as $i=>$r) {
                    $result['rows'][] = [$i+1,$r->tanggal?->format('Y-m-d'),$r->aset?->nama_aset ?? $r->kode_aset,$r->komponen?->nama_komponen ?? $r->kode_komponen,$r->total_downtime,$r->biaya_per_jam,$r->biaya_perbaikan,$r->total_estimasi];
                }
                $result['summary'] = ['total_estimasi' => $rows->sum('total_estimasi')];
                $monthlyTotals = $rows->groupBy(function($item){
                    return $item->tanggal?->format('Y-m') ?? 'Unknown';
                })->map(function($group){
                    return $group->sum('total_estimasi');
                });
                $result['chart'] = [
                    'type' => 'line',
                    'labels' => $monthlyTotals->keys()->toArray(),
                    'datasets' => [[
                        'label' => 'Total Estimasi',
                        'data' => $monthlyTotals->values()->toArray(),
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59,130,246,0.2)',
                        'fill' => true,
                    ]],
                ];
                break;

            case 'riwayat-pekerjaan':
                $result['title'] = 'Laporan Riwayat Pekerjaan';
                $result['description'] = 'Riwayat pekerjaan teknisi.';
                $result['columns'] = ['No','Tanggal Pelaksanaan','Nama Aset','Komponen','Status Pekerjaan','Hasil Pengecekan','Tindakan Dilakukan','Durasi','Biaya Maintenance'];
                $q = RiwayatPekerjaan::query();
                if ($start) $q->where('tanggal_pelaksanaan','>=',$start);
                if ($end) $q->where('tanggal_pelaksanaan','<=',$end);
                $rows = $q->orderBy('tanggal_pelaksanaan','desc')->get();
                foreach ($rows as $i=>$r) {
                    $result['rows'][] = [$i+1,$r->tanggal_pelaksanaan?->format('Y-m-d'),$r->jadwal?->nama_aset ?? $r->kode_aset,$r->jadwal?->nama_komponen ?? $r->kode_komponen,$r->status_pekerjaan,strip_tags($r->hasil_pengecekan ?? $r->hasil_pekerjaan),strip_tags($r->tindakan_dilakukan ?? $r->catatan_teknisi),$r->durasi_pekerjaan,$r->biaya_maintenance];
                }
                $result['summary'] = ['total_pekerjaan' => $rows->count()];
                $statusCounts = $rows->groupBy('status')->map->count();
                $result['chart'] = [
                    'type' => 'pie',
                    'labels' => $statusCounts->keys()->toArray(),
                    'datasets' => [[
                        'label' => 'Status Pekerjaan',
                        'data' => $statusCounts->values()->toArray(),
                        'backgroundColor' => ['#10b981','#ef4444','#f59e0b','#3b82f6'],
                    ]],
                ];
                break;

            case 'gabungan':
            default:
                $result['title'] = 'Laporan Gabungan';
                $result['description'] = 'Ringkasan keseluruhan.';
                $result['columns'] = ['Key','Value'];
                $rows = [
                    ['Total Aset', Aset::count()],
                    ['Total Kerusakan', Kerusakan::count()],
                    ['Total Jadwal Maintenance', JadwalMaintenance::count()],
                    ['Total Analisis RCM', FmeaAnalisis::count()],
                    ['Total Estimasi Biaya', EstimasiBiaya::sum('total_estimasi')],
                    ['Total Pekerjaan Selesai', JadwalMaintenance::where('status_jadwal','Selesai')->count()],
                ];
                foreach ($rows as $r) $result['rows'][] = $r;
                break;
        }

        return response()->json(['data' => $result]);
    }

    public function cetakPdf(Request $request)
    {
        $jenis = $request->query('jenis');
        $tanggalAwal = $request->query('tanggal_awal');
        $tanggalAkhir = $request->query('tanggal_akhir');

        // reuse api to get structured data
        $apiReq = Request::create('/api/laporan', 'GET', ['jenis' => $jenis, 'tanggal_awal' => $tanggalAwal, 'tanggal_akhir' => $tanggalAkhir]);
        $resp = app()->handle($apiReq);
        $content = json_decode($resp->getContent(), true);
        $data = $content['data'] ?? null;

        $fileName = 'laporan_' . ($jenis ?? 'all') . '_' . date('Ymd_His') . '.pdf';
        return Pdf::loadView('laporan_print', [
                'report' => $data,
                'periode' => [$tanggalAwal, $tanggalAkhir],
                'logoPath' => public_path('assets/logo_perumda.png')
            ])
            ->setPaper('a4', 'landscape')
            ->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $jenis = $request->query('jenis');
        $tanggalAwal = $request->query('tanggal_awal');
        $tanggalAkhir = $request->query('tanggal_akhir');

        $apiReq = Request::create('/api/laporan', 'GET', ['jenis' => $jenis, 'tanggal_awal' => $tanggalAwal, 'tanggal_akhir' => $tanggalAkhir]);
        $resp = app()->handle($apiReq);
        $content = json_decode($resp->getContent(), true);
        $data = $content['data'] ?? null;

        if (!$data) return Response::make('Tidak ada data', 204);

        $columns = $data['columns'] ?? [];
        $rows = $data['rows'] ?? [];
        $dayNow = date('Y-m-d');
        $title = 'LAPORAN PREVENTIVE MAINTENANCE - PERUMDA TIRTA MUSI PALEMBANG';
        $subtitle = 'Periode: ' . ($tanggalAwal ?: '-') . ' s/d ' . ($tanggalAkhir ?: '-') . ' | Tanggal Cetak: ' . $dayNow;

        $html = '<html><head><meta charset="UTF-8"><style>';
        $html .= 'body{font-family:Arial,sans-serif;font-size:11pt;color:#111;}';
        $html .= 'table{border-collapse:collapse;width:100%;}';
        $html .= 'th,td{border:1px solid #333;padding:6px;text-align:left;vertical-align:top;}';
        $html .= '.title{font-size:14pt;font-weight:bold;padding:8px 0;}';
        $html .= '.subtitle{font-size:11pt;font-style:italic;padding:4px 0 10px 0;}';
        $html .= '.header-row{background:#0f172a;color:#ffffff;font-weight:bold;}';
        $html .= '</style></head><body>';
        $html .= '<table><tr><td colspan="' . max(1, count($columns)) . '" class="title">' . htmlspecialchars($title) . '</td></tr>';
        $html .= '<tr><td colspan="' . max(1, count($columns)) . '" class="subtitle">' . htmlspecialchars($subtitle) . '</td></tr>';
        $html .= '<tr><td colspan="' . max(1, count($columns)) . '" style="height:18px;border:none;"></td></tr>';
        $html .= '<tr class="header-row">';
        foreach ($columns as $column) {
            $html .= '<th>' . htmlspecialchars($column) . '</th>';
        }
        $html .= '</tr>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars(strip_tags((string)$cell)) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        $fileName = 'laporan_' . ($jenis ?? 'all') . '_' . date('Ymd_His') . '.xls';
        return Response::make($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
