<?php

namespace App\Http\Controllers;

use App\Models\JadwalMaintenance;
use App\Models\Kerusakan;
use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KerusakanController extends Controller
{
    public function indexView()
    {
        return view('datakerusakanadm');
    }

    public function inputView()
    {
        $assets = Aset::orderBy('nama_aset')->get(['kode_aset', 'nama_aset']);
        return view('inputkerusakantks', compact('assets'));
    }

    public function index(Request $request)
    {
        $query = Kerusakan::with(['aset', 'komponen', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kode_aset')) {
            $query->where('kode_aset', $request->kode_aset);
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_kerusakan', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_kerusakan', '<=', $request->sampai_tanggal);
        }

        $kerusakan = $query->orderByDesc('tanggal_kerusakan')->get();

        $summary = [
            'total' => Kerusakan::count(),
            'total_aset' => Aset::count(),
            'total_maintenance' => JadwalMaintenance::count(),
            'belum' => Kerusakan::where('status', 'Belum Ditangani')->count(),
            'proses' => Kerusakan::where('status', 'Diproses')->count(),
            'selesai' => Kerusakan::where('status', 'Selesai')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $kerusakan,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'tanggal_kerusakan' => 'required|date',
        'kode_aset' => 'required|string|exists:aset,kode_aset',
        'kode_komponen' => 'required|string|exists:komponen,kode_komponen',
        'jenis_kerusakan' => 'required|string|max:255',
        'deskripsi_kerusakan' => 'nullable|string',
        'downtime_jam' => ['required', 'numeric', 'min:0.01'],
        'teknisi_pelapor' => 'nullable|string|max:150',
        'status' => 'required|in:Belum Ditangani,Diproses,Selesai',
        'estimasi_biaya' => ['required', 'numeric', 'min:0.01'],
        'catatan_teknisi' => 'nullable|string',
        'foto_kerusakan' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
    ]);

    $teknisi = Auth::check()
        ? Auth::user()->name
        : ($validated['teknisi_pelapor'] ?? 'Teknisi');

    $fotoPath = null;

    if ($request->hasFile('foto_kerusakan')) {
        $fotoPath = $request->file('foto_kerusakan')->store('kerusakan', 'public');
    }

    $kerusakan = Kerusakan::create([
        'tanggal_kerusakan' => $validated['tanggal_kerusakan'],
        'kode_aset' => $validated['kode_aset'],
        'kode_komponen' => $validated['kode_komponen'],
        'jenis_kerusakan' => $validated['jenis_kerusakan'],
        'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'] ?? null,
        'downtime_jam' => $validated['downtime_jam'],
        'teknisi_pelapor' => $teknisi,
        'user_id' => Auth::id(),
        'status' => $validated['status'],
        'estimasi_biaya' => $validated['estimasi_biaya'],
        'catatan_teknisi' => $validated['catatan_teknisi'] ?? null,
        'foto_kerusakan' => $fotoPath,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Data kerusakan berhasil disimpan.',
        'data' => $kerusakan,
    ], 201);
}

    public function update(Request $request, Kerusakan $kerusakan)
    {
        $validated = $request->validate([
            'tanggal_kerusakan' => 'sometimes|date',
            'kode_aset' => 'sometimes|string|exists:aset,kode_aset',
            'kode_komponen' => 'sometimes|string|exists:komponen,kode_komponen',
            'jenis_kerusakan' => 'sometimes|string|max:255',
            'deskripsi_kerusakan' => 'nullable|string',
            'downtime_jam' => ['sometimes','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'status' => 'sometimes|in:Belum Ditangani,Diproses,Selesai',
            'estimasi_biaya' => ['sometimes','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'catatan_teknisi' => 'nullable|string',
        ]);

        $kerusakan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data kerusakan berhasil diperbarui.',
            'data' => $kerusakan,
        ]);
    }

    public function destroy(Kerusakan $kerusakan)
    {
        $kerusakan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kerusakan berhasil dihapus.',
        ]);
    }
}
