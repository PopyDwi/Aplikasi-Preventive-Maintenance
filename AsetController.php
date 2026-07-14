<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Komponen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsetController extends Controller
{
    public function index()
    {
        $assets = Aset::with('komponen')->orderBy('id')->get();
        $teknisis = User::where('role', 'Teknisi')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('dataasetadm', [
            'assets' => $assets,
            'nextKodeAset' => $this->getNextAsetCode(),
            'nextKodeKomponen' => $this->getNextKomponenCode(),
            'teknisis' => $teknisis,
        ]);
    }

    public function getKomponenByAset($kode_aset)
    {
        $komponen = Komponen::where('kode_aset', $kode_aset)
            ->orderBy('nama_komponen')
            ->get(['kode_komponen', 'nama_komponen']);

        return response()->json([
            'success' => true,
            'data' => $komponen,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_aset' => 'required|string|max:10|unique:aset,kode_aset',
            'nama_aset' => 'required|string|max:150',
            'status' => 'nullable|string|max:50',
            'tanggal_instalasi' => 'nullable|date',
            'penanggungjawab' => 'nullable|string|max:150',
            'komponen' => 'required|array|min:1',
            'komponen.*.nama_komponen' => 'required|string|max:150',
            'komponen.*.fungsi_keterangan' => 'nullable|string',
            'komponen.*.volume' => 'nullable|integer|min:0',
            'komponen.*.satuan' => 'nullable|string|max:50',
            'komponen.*.harga_satuan' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $aset = Aset::create([
                'kode_aset' => $validated['kode_aset'],
                'nama_aset' => $validated['nama_aset'],
                'status' => $validated['status'] ?? 'Normal',
                'tanggal_instalasi' => $validated['tanggal_instalasi'] ?? null,
                'penanggungjawab' => $validated['penanggungjawab'] ?? null,
            ]);

            foreach ($validated['komponen'] as $komponenData) {
                $harga = isset($komponenData['harga_satuan']) ? floatval($komponenData['harga_satuan']) : 0;
                $volume = isset($komponenData['volume']) ? intval($komponenData['volume']) : 0;
                Komponen::create([
                    'kode_komponen' => $this->getNextKomponenCode(),
                    'kode_aset' => $aset->kode_aset,
                    'nama_komponen' => $komponenData['nama_komponen'],
                    'fungsi_keterangan' => $komponenData['fungsi_keterangan'] ?? null,
                    'volume' => $volume,
                    'satuan' => $komponenData['satuan'] ?? null,
                    'harga_satuan' => $harga,
                    'total_biaya' => $volume * $harga,
                ]);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('dataaset.index')->with('success', 'Aset berhasil disimpan.');
    }

    public function update(Request $request, $kode_aset)
    {
        $aset = Aset::where('kode_aset', $kode_aset)->firstOrFail();

        $validated = $request->validate([
            'nama_aset' => 'required|string|max:150',
            'status' => 'nullable|string|max:50',
            'tanggal_instalasi' => 'nullable|date',
            'penanggungjawab' => 'nullable|string|max:150',
            'komponen' => 'required|array|min:1',
            'komponen.*.kode_komponen' => 'nullable|string|max:10',
            'komponen.*.nama_komponen' => 'required|string|max:150',
            'komponen.*.fungsi_keterangan' => 'nullable|string',
            'komponen.*.volume' => 'nullable|integer|min:0',
            'komponen.*.satuan' => 'nullable|string|max:50',
            'komponen.*.harga_satuan' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($aset, $validated) {
            $aset->update([
                'nama_aset' => $validated['nama_aset'],
                'status' => $validated['status'] ?? 'Normal',
                'tanggal_instalasi' => $validated['tanggal_instalasi'] ?? null,
                'penanggungjawab' => $validated['penanggungjawab'] ?? null,
            ]);

            $aset->komponen()->delete();

            foreach ($validated['komponen'] as $komponenData) {
                $harga = isset($komponenData['harga_satuan']) ? floatval($komponenData['harga_satuan']) : 0;
                $volume = isset($komponenData['volume']) ? intval($komponenData['volume']) : 0;
                Komponen::create([
                    'kode_komponen' => $komponenData['kode_komponen'] ?? $this->getNextKomponenCode(),
                    'kode_aset' => $aset->kode_aset,
                    'nama_komponen' => $komponenData['nama_komponen'],
                    'fungsi_keterangan' => $komponenData['fungsi_keterangan'] ?? null,
                    'volume' => $volume,
                    'satuan' => $komponenData['satuan'] ?? null,
                    'harga_satuan' => $harga,
                    'total_biaya' => $volume * $harga,
                ]);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('dataaset.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Request $request, $kode_aset)
    {
        $aset = Aset::where('kode_aset', $kode_aset)->firstOrFail();
        
        DB::transaction(function () use ($aset, $kode_aset) {
            Komponen::where('kode_aset', $kode_aset)->delete();
            $aset->delete();
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('dataaset.index')->with('success', 'Aset berhasil dihapus.');
    }

    private function getNextAsetCode(): string
    {
        $last = Aset::orderBy('kode_aset', 'desc')->first();

        if (!$last) {
            return 'AST001';
        }

        if (preg_match('/AST(\d+)/', $last->kode_aset, $matches)) {
            return 'AST' . str_pad((int) $matches[1] + 1, 3, '0', STR_PAD_LEFT);
        }

        return 'AST001';
    }

    private function getNextKomponenCode(): string
    {
        $last = Komponen::orderBy('kode_komponen', 'desc')->first();

        if (!$last) {
            return 'KMP001';
        }

        if (preg_match('/KMP(\d+)/', $last->kode_komponen, $matches)) {
            return 'KMP' . str_pad((int) $matches[1] + 1, 3, '0', STR_PAD_LEFT);
        }

        return 'KMP001';
    }
}
