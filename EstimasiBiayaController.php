<?php

namespace App\Http\Controllers;

use App\Models\EstimasiBiaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstimasiBiayaController extends Controller
{
    public function index(Request $request)
    {
        $query = EstimasiBiaya::with(['aset', 'komponen']);

        if ($request->filled('kode_aset')) {
            $query->where('kode_aset', $request->kode_aset);
        }

        $data = $query->orderByDesc('tanggal')->get();

        return response()->json([ 'success' => true, 'data' => $data ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kode_aset' => 'required|string|exists:aset,kode_aset',
            'kode_komponen' => 'nullable|string|exists:komponen,kode_komponen',
            'total_downtime' => ['required','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'biaya_per_jam' => ['required','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'biaya_perbaikan' => ['required','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'keterangan' => 'nullable|string',
        ]);

        $totalEstimasi = ($validated['total_downtime'] * $validated['biaya_per_jam']) + $validated['biaya_perbaikan'];

        $estimasi = EstimasiBiaya::create([
            'tanggal' => $validated['tanggal'],
            'kode_aset' => $validated['kode_aset'],
            'kode_komponen' => $validated['kode_komponen'] ?? null,
            'total_downtime' => $validated['total_downtime'],
            'biaya_per_jam' => $validated['biaya_per_jam'],
            'biaya_perbaikan' => $validated['biaya_perbaikan'],
            'total_estimasi' => $totalEstimasi,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return response()->json([ 'success' => true, 'message' => 'Estimasi biaya berhasil disimpan.', 'data' => $estimasi ], 201);
    }

    public function update(Request $request, EstimasiBiaya $estimasi)
    {
        $validated = $request->validate([
            'tanggal' => 'sometimes|date',
            'kode_aset' => 'sometimes|string|exists:aset,kode_aset',
            'kode_komponen' => 'nullable|string|exists:komponen,kode_komponen',
            'total_downtime' => ['sometimes','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'biaya_per_jam' => ['sometimes','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'biaya_perbaikan' => ['sometimes','regex:/^\d+(?:\.\d+)?$/','numeric','min:0.01'],
            'keterangan' => 'nullable|string',
        ]);

        if (isset($validated['total_downtime']) || isset($validated['biaya_per_jam']) || isset($validated['biaya_perbaikan'])) {
            $downtime = $validated['total_downtime'] ?? $estimasi->total_downtime;
            $perJam = $validated['biaya_per_jam'] ?? $estimasi->biaya_per_jam;
            $perbaikan = $validated['biaya_perbaikan'] ?? $estimasi->biaya_perbaikan;
            $validated['total_estimasi'] = ($downtime * $perJam) + $perbaikan;
        }

        $estimasi->update($validated);

        return response()->json([ 'success' => true, 'message' => 'Estimasi biaya berhasil diperbarui.', 'data' => $estimasi ]);
    }

    public function destroy(EstimasiBiaya $estimasi)
    {
        $estimasi->delete();
        return response()->json([ 'success' => true, 'message' => 'Estimasi biaya berhasil dihapus.' ]);
    }
}
