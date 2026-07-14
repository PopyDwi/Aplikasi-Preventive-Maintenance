<?php

namespace App\Http\Controllers;

use App\Models\JadwalMaintenance;
use App\Models\FmeaAnalisis;
use App\Models\Aset;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalMaintenanceController extends Controller
{
    /**
     * Store - Menyimpan jadwal maintenance baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_aset' => 'required|string|max:50',
            'kode_komponen' => 'required|string|max:50',
            'tanggal_maintenance' => 'required|date',
            'prioritas' => 'required|in:Sangat Tinggi,Tinggi,Sedang,Rendah',
            'tindakan_maintenance' => 'required|string',
            'status_jadwal' => 'required|in:Dijadwalkan,Diproses,Selesai,Dibatalkan',
            'catatan_tambahan' => 'nullable|string',
            'mode_kegagalan' => 'nullable|string|max:200',
            'kategori_risiko' => 'nullable|string|max:50',
            'rpn' => 'nullable|integer',
            'analisis_rcm_id' => 'nullable|exists:analisis_rcm,id',
        ]);

        // Cari penanggungjawab dari aset
        $penanggungJawab = Aset::where('kode_aset', $validated['kode_aset'])->value('penanggungjawab');

        $nomorWhatsapp = null;
        if ($penanggungJawab) {
            $nomorWhatsapp = User::where('name', $penanggungJawab)->value('nomor_whatsapp');
        }

        $jadwal = JadwalMaintenance::create([
            'analisis_rcm_id' => $validated['analisis_rcm_id'] ?? null,
            'kode_aset' => $validated['kode_aset'],
            'kode_komponen' => $validated['kode_komponen'],
            'tanggal_maintenance' => $validated['tanggal_maintenance'],
            'prioritas' => $validated['prioritas'],
            'status_jadwal' => $validated['status_jadwal'] ?? 'Dijadwalkan',
            'tindakan_maintenance' => $validated['tindakan_maintenance'],
            'catatan_tambahan' => $validated['catatan_tambahan'] ?? null,
            'mode_kegagalan' => $validated['mode_kegagalan'] ?? null,
            'kategori_risiko' => $validated['kategori_risiko'] ?? null,
            'rpn' => $validated['rpn'] ?? null,
            'penanggungjawab' => $penanggungJawab,
            'nomor_whatsapp' => $nomorWhatsapp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal maintenance berhasil disimpan',
            'data' => $jadwal->load(['aset', 'komponen', 'fmeaAnalisis'])
        ], 201);
    }

    /**
     * Index - Menampilkan semua jadwal maintenance
     */
    public function index(Request $request)
    {
        // Pastikan semua analisis RCM memiliki jadwal maintenance otomatis
        $existingAnalisisIds = JadwalMaintenance::whereNotNull('analisis_rcm_id')
            ->pluck('analisis_rcm_id')
            ->toArray();

        $missingAnalisis = FmeaAnalisis::whereNotIn('id', $existingAnalisisIds)->get();

        foreach ($missingAnalisis as $analisis) {
            $penanggungJawab = Aset::where('kode_aset', $analisis->kode_aset)->value('penanggungjawab');
            $nomorWhatsapp = null;
            if ($penanggungJawab) {
                $nomorWhatsapp = User::where('name', $penanggungJawab)->value('nomor_whatsapp');
            }

            JadwalMaintenance::create([
                'analisis_rcm_id' => $analisis->id,
                'kode_aset' => $analisis->kode_aset,
                'kode_komponen' => $analisis->kode_komponen,
                'tanggal_maintenance' => $analisis->tanggal_jadwal_berikutnya ?? Carbon::now()->toDateString(),
                'prioritas' => $analisis->kategori_risiko ?? 'Rendah',
                'status_jadwal' => 'Dijadwalkan',
                'tindakan_maintenance' => $analisis->rekomendasi_perawatan ?? '-',
                'catatan_tambahan' => null,
                'mode_kegagalan' => $analisis->mode_kegagalan ?? null,
                'kategori_risiko' => $analisis->kategori_risiko ?? null,
                'rpn' => $analisis->rpn ?? null,
                'penanggungjawab' => $penanggungJawab,
                'nomor_whatsapp' => $nomorWhatsapp,
            ]);
        }

        $query = JadwalMaintenance::with(['fmeaAnalisis', 'aset', 'komponen']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_jadwal', $request->status);
        }

        // Filter berdasarkan prioritas
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }

        // Filter berdasarkan kode aset
        if ($request->filled('kode_aset')) {
            $query->where('kode_aset', $request->kode_aset);
        }

        // Filter berdasarkan range tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_maintenance', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_maintenance', '<=', $request->sampai_tanggal);
        }

        $perPage = $request->get('per_page', 50);
        $jadwal = $query->orderBy('tanggal_maintenance', 'asc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $jadwal
        ]);
    }

    /**
     * Show - Menampilkan detail jadwal tertentu
     */
    public function show(JadwalMaintenance $jadwalMaintenance)
    {
        $jadwal = $jadwalMaintenance->load(['fmeaAnalisis', 'aset', 'komponen']);

        $data = [
            'id' => $jadwal->id,
            'kode_aset' => $jadwal->kode_aset,
            'nama_aset' => ($jadwal->relationLoaded('aset') && $jadwal->aset) ? $jadwal->aset->nama_aset : ($jadwal->kode_aset ?? null),
            'kode_komponen' => $jadwal->kode_komponen,
            'nama_komponen' => ($jadwal->relationLoaded('komponen') && $jadwal->komponen) ? $jadwal->komponen->nama_komponen : ($jadwal->kode_komponen ?? null),
            'tanggal_maintenance' => $jadwal->tanggal_maintenance,
            'prioritas' => $jadwal->prioritas,
            'status_jadwal' => $jadwal->status_jadwal ?? ($jadwal->attributes['status_jadwal'] ?? null),
            'tindakan_maintenance' => $jadwal->tindakan_maintenance,
            'catatan_tambahan' => $jadwal->catatan_tambahan,
            'penanggungjawab' => $jadwal->penanggungjawab,
            'nomor_whatsapp' => $jadwal->nomor_whatsapp,
            'mode_kegagalan' => $jadwal->mode_kegagalan,
            'kategori_risiko' => $jadwal->kategori_risiko,
            'rpn' => $jadwal->rpn,
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Update - Memperbarui jadwal maintenance
     */
    public function update(Request $request, JadwalMaintenance $jadwalMaintenance)
    {
        $validated = $request->validate([
            'kode_aset' => 'sometimes|string|max:50',
            'kode_komponen' => 'sometimes|string|max:50',
            'tanggal_maintenance' => 'sometimes|date',
            'prioritas' => 'sometimes|in:Sangat Tinggi,Tinggi,Sedang,Rendah',
            'tindakan_maintenance' => 'sometimes|string',
            'status_jadwal' => 'sometimes|in:Dijadwalkan,Diproses,Selesai,Dibatalkan',
            'catatan_tambahan' => 'nullable|string',
            'mode_kegagalan' => 'nullable|string|max:200',
            'kategori_risiko' => 'nullable|string|max:50',
        ]);

        $jadwalMaintenance->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal maintenance berhasil diperbarui',
            'data' => $jadwalMaintenance->load(['fmeaAnalisis', 'aset', 'komponen'])
        ]);
    }

    /**
     * Destroy - Menghapus jadwal maintenance
     */
    public function destroy(JadwalMaintenance $jadwalMaintenance)
    {
        $jadwalMaintenance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal maintenance berhasil dihapus'
        ]);
    }

    /**
     * updateStatus - Mengupdate status jadwal
     */
    public function updateStatus(Request $request, JadwalMaintenance $jadwalMaintenance)
    {
        $validated = $request->validate([
            'status_jadwal' => 'required|in:Dijadwalkan,Diproses,Selesai,Dibatalkan',
        ]);

        $jadwalMaintenance->update(['status_jadwal' => $validated['status_jadwal']]);

        return response()->json([
            'success' => true,
            'message' => 'Status jadwal berhasil diperbarui',
            'data' => $jadwalMaintenance->load(['fmeaAnalisis', 'aset', 'komponen'])
        ]);
    }

    /**
     * getJadwalMendatang - Mendapatkan jadwal maintenance yang akan datang
     */
    public function getJadwalMendatang(Request $request)
    {
        $hari = $request->get('hari', 7); // Default 7 hari ke depan
        
        $jadwal = JadwalMaintenance::whereBetween('tanggal_maintenance', [
                Carbon::now()->toDateString(),
                Carbon::now()->addDays($hari)->toDateString()
            ])
            ->where('status_jadwal', '!=', 'Selesai')
            ->orderBy('tanggal_maintenance', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'jadwal_mendatang' => $hari . ' hari',
            'count' => $jadwal->count(),
            'data' => $jadwal
        ]);
    }

    /**
     * getJadwalTerlambat - Mendapatkan jadwal yang sudah lewat tapi belum selesai
     */
    public function getJadwalTerlambat()
    {
        $jadwal = JadwalMaintenance::where('tanggal_maintenance', '<', Carbon::now()->toDateString())
            ->where('status_jadwal', '!=', 'Selesai')
            ->orderBy('tanggal_maintenance', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $jadwal->count(),
            'data' => $jadwal
        ]);
    }

    /**
     * getStatistik - Mendapatkan statistik jadwal maintenance
     */
    public function getStatistik()
    {
        $total = JadwalMaintenance::count();
        $belum = JadwalMaintenance::where('status_jadwal', 'Dijadwalkan')->count();
        $selesai = JadwalMaintenance::where('status_jadwal', 'Selesai')->count();

        $sangattinggi = JadwalMaintenance::where('prioritas', 'Sangat Tinggi')->count();
        $tinggi = JadwalMaintenance::where('prioritas', 'Tinggi')->count();
        $sedang = JadwalMaintenance::where('prioritas', 'Sedang')->count();
        $rendah = JadwalMaintenance::where('prioritas', 'Rendah')->count();

        $terlambat = JadwalMaintenance::where('tanggal_maintenance', '<', Carbon::now()->toDateString())
            ->where('status_jadwal', '!=', 'Selesai')
            ->count();

        return response()->json([
            'success' => true,
            'statistik' => [
                'total_jadwal' => $total,
                'belum_dikerjakan' => $belum,
                'selesai_dikerjakan' => $selesai,
                'sangat_tinggi' => $sangattinggi,
                'tinggi' => $tinggi,
                'sedang' => $sedang,
                'rendah' => $rendah,
                'terlambat' => $terlambat
            ]
        ]);
    }

    /**
     * getByMesin - Mendapatkan jadwal berdasarkan mesin
     */
    public function getByMesin($kodeMesin)
    {
        $jadwal = JadwalMaintenance::where('kode_aset', $kodeMesin)
            ->orderBy('tanggal_maintenance', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'kode_mesin' => $kodeMesin,
            'count' => $jadwal->count(),
            'data' => $jadwal
        ]); 
    }

    /**
     * getByTeknisi - Mendapatkan jadwal berdasarkan teknisi
     */
    public function getByTeknisi($teknisi)
    {
        $jadwal = JadwalMaintenance::where('penanggungjawab', $teknisi)
            ->where('status_jadwal', '!=', 'Selesai')
            ->orderBy('tanggal_maintenance', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'teknisi' => $teknisi,
            'count' => $jadwal->count(),
            'data' => $jadwal
        ]);
    }
}
