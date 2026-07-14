<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPekerjaan;
use App\Models\JadwalMaintenance;
use Illuminate\Http\Request;

class RiwayatPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $query = RiwayatPekerjaan::with(['jadwal.aset', 'jadwal.komponen'])->orderBy('tanggal_pelaksanaan', 'desc');

        if ($request->filled('status')) {
            $query->where('status_pekerjaan', RiwayatPekerjaan::normalizeStatus($request->status));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('hasil_pengecekan', 'like', "%{$search}%")
                    ->orWhere('tindakan_dilakukan', 'like', "%{$search}%")
                    ->orWhere('teknisi', 'like', "%{$search}%");
            });
        }

        $riwayat = $query->paginate($request->get('per_page', 50));

        return response()->json(['success' => true, 'data' => $riwayat]);
    }

    public function show(RiwayatPekerjaan $riwayatPekerjaan)
    {
        $riwayatPekerjaan->load(['jadwal.aset', 'jadwal.komponen']);

        return response()->json(['success' => true, 'data' => $riwayatPekerjaan]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_maintenance_id' => 'required|exists:jadwal_maintenance,id',
            'tanggal_pelaksanaan' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'teknisi' => 'nullable|string|max:100',
            'penanggungjawab' => 'nullable|string|max:150',
            'nomor_whatsapp' => 'nullable|string|max:25',
            'hasil_pengecekan' => 'nullable|string',
            'hasil_pekerjaan' => 'nullable|string',
            'catatan_teknisi' => 'nullable|string',
            'tindakan_dilakukan' => 'nullable|string',
            'durasi_pekerjaan' => ['nullable','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'biaya_maintenance' => ['nullable','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'dokumentasi' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        // Handle file upload if present
        $dokumentasiPath = null;
        if ($request->hasFile('dokumentasi')) {
            $file = $request->file('dokumentasi');
            $dokumentasiPath = $file->store('riwayat_pekerjaan', 'public');
        }

        // Determine teknisi from authenticated user if available
        $teknisiName = auth()->check() ? auth()->user()->name : ($validated['teknisi'] ?? null);

        $jadwal = JadwalMaintenance::find($validated['jadwal_maintenance_id']);

        $status = $validated['status'] ?? 'Selesai';

        $riwayat = RiwayatPekerjaan::create([
            'jadwal_maintenance_id' => $validated['jadwal_maintenance_id'],
            'kode_aset' => $jadwal?->kode_aset,
            'kode_komponen' => $jadwal?->kode_komponen,
            'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'] ?? now()->toDateString(),
            'teknisi' => $teknisiName,
            'penanggungjawab' => $jadwal?->penanggungjawab,
            'nomor_whatsapp' => $jadwal?->nomor_whatsapp,
            'status_pekerjaan' => $status,
            'hasil_pengecekan' => $validated['hasil_pengecekan'] ?? null,
            'tindakan_dilakukan' => $validated['tindakan_dilakukan'] ?? null,
            'durasi_pekerjaan' => $validated['durasi_pekerjaan'] ?? null,
            'biaya_maintenance' => $validated['biaya_maintenance'] ?? null,
            'dokumentasi' => $dokumentasiPath,
        ]);

        // Update jadwal status jika terkait
        if ($jadwal) {
            $jadwal->status_jadwal = 'Selesai';
            $jadwal->save();
        }

        return response()->json(['success' => true, 'message' => 'Data maintenance berhasil disimpan.', 'data' => $riwayat], 201);
    }

    public function update(Request $request, RiwayatPekerjaan $riwayatPekerjaan)
    {
        $validated = $request->validate([
            'hasil_pengecekan' => 'nullable|string',
            'hasil_pekerjaan' => 'nullable|string',
            'catatan_teknisi' => 'nullable|string',
            'tindakan' => 'nullable|string',
            'durasi_jam' => 'nullable|numeric',
            'biaya' => 'nullable|numeric',
            'dokumentasi' => 'nullable|string',
        ]);

        $riwayatPekerjaan->update($validated);

        if ($riwayatPekerjaan->jadwal_maintenance_id) {
            $jadwal = JadwalMaintenance::find($riwayatPekerjaan->jadwal_maintenance_id);
            if ($jadwal && $jadwal->status_jadwal !== 'Selesai') {
                $jadwal->status_jadwal = 'Selesai';
                $jadwal->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Riwayat pekerjaan diperbarui', 'data' => $riwayatPekerjaan]);
    }
}
