<?php

namespace App\Http\Controllers;

use App\Models\JadwalMaintenance;
use App\Models\Kerusakan;
use App\Models\RiwayatPekerjaan;
use Carbon\Carbon;

class DashboardTeknisiController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $jadwalHariIni = JadwalMaintenance::whereDate('tanggal_maintenance', $today)
            ->where('status_jadwal', 'Dijadwalkan')
            ->count();

        $kerusakanDilaporkan = Kerusakan::when(
            auth()->check(),
            fn ($query) => $query->where('user_id', auth()->id()),
            fn ($query) => $query
        )->count();

        $totalJadwal = JadwalMaintenance::count();
        $belum = JadwalMaintenance::where('status_jadwal', 'Dijadwalkan')->count();
        $selesai = JadwalMaintenance::where('status_jadwal', 'Selesai')->count();
        $maintenanceSelesai = $selesai;

        $daftarJadwal = JadwalMaintenance::with(['aset', 'komponen'])
            ->where('status_jadwal', 'Dijadwalkan')
            ->orderBy('tanggal_maintenance', 'asc')
            ->get();

        $jadwalTerdekat = JadwalMaintenance::where('status_jadwal', 'Dijadwalkan')
            ->orderBy('tanggal_maintenance', 'asc')
            ->first();

        return view('dashboardteknisi', compact(
            'jadwalHariIni',
            'kerusakanDilaporkan',
            'maintenanceSelesai',
            'daftarJadwal',
            'totalJadwal',
            'belum',
            'selesai',
            'jadwalTerdekat'
        ));
    }
}
