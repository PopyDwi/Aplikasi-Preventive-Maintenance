<?php

namespace App\Http\Controllers;

use App\Models\FmeaAnalisis;
use App\Models\Aset;
use App\Models\Komponen;
use App\Models\JadwalMaintenance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FmeaAnalisisController extends Controller
{
    private function getKategoriRisiko($rpn)
    {
        if ($rpn >= 300) return 'Sangat Tinggi';
        if ($rpn >= 200) return 'Tinggi';
        if ($rpn >= 100) return 'Sedang';
        return 'Rendah';
    }

    private function getIntervalMaintenance($kategori)
    {
        return [
            'Sangat Tinggi' => ['hari' => 7, 'text' => 'Setiap 7 hari'],
            'Tinggi' => ['hari' => 14, 'text' => 'Setiap 14 hari'],
            'Sedang' => ['hari' => 30, 'text' => 'Setiap 30 hari'],
            'Rendah' => ['hari' => 90, 'text' => 'Setiap 90 hari'],
        ][$kategori] ?? ['hari' => 90, 'text' => 'Setiap 90 hari'];
    }

    private function getRekomendasi($kategori, $modeKegagalan = null)
    {
        $rekomendasi = [
            'Sangat Tinggi' => 'Segera lakukan preventive maintenance, inspeksi menyeluruh, dan prioritaskan penggantian/perbaikan komponen.',
            'Tinggi' => 'Lakukan pemeriksaan rutin, monitoring kondisi komponen, dan jadwalkan preventive maintenance dalam waktu dekat.',
            'Sedang' => 'Lakukan perawatan berkala, pembersihan, dan pengecekan fungsi komponen.',
            'Rendah' => 'Lakukan monitoring normal dan pemeriksaan visual secara rutin.'
        ];

        $result = $rekomendasi[$kategori] ?? $rekomendasi['Rendah'];

        if ($modeKegagalan) {
            $lowerMode = strtolower($modeKegagalan);

            if (str_contains($lowerMode, 'bearing')) {
                $result .= ' Fokus pada pelumasan dan pemeriksaan getaran bearing.';
            } elseif (str_contains($lowerMode, 'overheat')) {
                $result .= ' Periksa suhu motor, sistem pendingin, dan beban kerja mesin.';
            } elseif (str_contains($lowerMode, 'bocor')) {
                $result .= ' Lakukan pemeriksaan seal, sambungan, dan tekanan pompa.';
            }
        }

        return $result;
    }

    private function getPenanggungJawabAset($kodeAset): ?string
    {
        return Aset::where('kode_aset', $kodeAset)->value('penanggungjawab');
    }

    public function view()
    {
        $assets = Aset::orderBy('nama_aset')->get();
        return view('analisisrcmadm', compact('assets'));
    }

    public function inputTeknisi()
    {
        $assets = Aset::orderBy('nama_aset')->get();
        return view('inputrisikokerusakan', compact('assets'));
    }

    public function index()
    {
        $analisis = FmeaAnalisis::with(['aset', 'komponenRel', 'jadwalMaintenance'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $analisis
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kode_aset' => 'required|string|max:10|exists:aset,kode_aset',
                'kode_komponen' => 'required|string|max:10|exists:komponen,kode_komponen',
                'mode_kegagalan' => 'required|string|max:200',
                'dampak_kegagalan' => 'required|string',
                'severity' => ['required', 'integer', 'regex:/^(?:[1-9]|10)$/', 'min:1', 'max:10'],
                'occurrence' => ['required', 'integer', 'regex:/^(?:[1-9]|10)$/', 'min:1', 'max:10'],
                'detection' => ['required', 'integer', 'regex:/^(?:[1-9]|10)$/', 'min:1', 'max:10'],
            ]);

            return DB::transaction(function () use ($validated) {
                $rpn = $validated['severity'] * $validated['occurrence'] * $validated['detection'];
                $kategori = $this->getKategoriRisiko($rpn);
                $interval = $this->getIntervalMaintenance($kategori);
                $rekomendasi = $this->getRekomendasi($kategori, $validated['mode_kegagalan']);
                $jadwalBerikutnya = Carbon::now()->addDays($interval['hari'])->toDateString();

                $analisis = FmeaAnalisis::create([
                    'kode_aset' => $validated['kode_aset'],
                    'kode_komponen' => $validated['kode_komponen'],
                    'mode_kegagalan' => $validated['mode_kegagalan'],
                    'dampak_kegagalan' => $validated['dampak_kegagalan'],
                    'severity' => $validated['severity'],
                    'occurrence' => $validated['occurrence'],
                    'detection' => $validated['detection'],
                    'rpn' => $rpn,
                    'kategori_risiko' => $kategori,
                    'rekomendasi_perawatan' => $rekomendasi,
                    'jadwal_maintenance_berikutnya' => $jadwalBerikutnya,
                ]);

                $penanggungJawab = $this->getPenanggungJawabAset($analisis->kode_aset);

                $nomorWhatsapp = null;
                if ($penanggungJawab) {
                    $nomorWhatsapp = User::where('name', $penanggungJawab)->value('nomor_whatsapp');
                }

                $jadwal = JadwalMaintenance::create([
                    'analisis_rcm_id' => $analisis->id,
                    'kode_aset' => $analisis->kode_aset,
                    'kode_komponen' => $analisis->kode_komponen,
                    'tanggal_maintenance' => $jadwalBerikutnya,
                    'prioritas' => $kategori,
                    'status_jadwal' => 'Dijadwalkan',
                    'tindakan_maintenance' => $rekomendasi,
                    'catatan_tambahan' => null,
                    'penanggungjawab' => $penanggungJawab,
                    'nomor_whatsapp' => $nomorWhatsapp,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Analisis risiko berhasil disimpan dan jadwal maintenance otomatis dibuat.',
                    'data' => $analisis->load(['aset', 'komponenRel']),
                    'jadwal_maintenance_id' => $jadwal->id,
                    'rpn' => $rpn,
                    'kategori' => $kategori,
                    'rekomendasi' => $rekomendasi,
                    'jadwal_berikutnya' => $jadwalBerikutnya,
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan analisis risiko: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, FmeaAnalisis $fmeaAnalisis)
    {
        try {
            $validated = $request->validate([
                'kode_aset' => 'required|string|max:10|exists:aset,kode_aset',
                'kode_komponen' => 'required|string|max:10|exists:komponen,kode_komponen',
                'mode_kegagalan' => 'required|string|max:200',
                'dampak_kegagalan' => 'required|string',
                'severity' => ['required', 'integer', 'regex:/^(?:[1-9]|10)$/', 'min:1', 'max:10'],
                'occurrence' => ['required', 'integer', 'regex:/^(?:[1-9]|10)$/', 'min:1', 'max:10'],
                'detection' => ['required', 'integer', 'regex:/^(?:[1-9]|10)$/', 'min:1', 'max:10'],
            ]);

            return DB::transaction(function () use ($validated, $fmeaAnalisis) {
                $rpn = $validated['severity'] * $validated['occurrence'] * $validated['detection'];
                $kategori = $this->getKategoriRisiko($rpn);
                $interval = $this->getIntervalMaintenance($kategori);
                $rekomendasi = $this->getRekomendasi($kategori, $validated['mode_kegagalan']);
                $jadwalBerikutnya = Carbon::now()->addDays($interval['hari'])->toDateString();

                $fmeaAnalisis->update([
                    'kode_aset' => $validated['kode_aset'],
                    'kode_komponen' => $validated['kode_komponen'],
                    'mode_kegagalan' => $validated['mode_kegagalan'],
                    'dampak_kegagalan' => $validated['dampak_kegagalan'],
                    'severity' => $validated['severity'],
                    'occurrence' => $validated['occurrence'],
                    'detection' => $validated['detection'],
                    'rpn' => $rpn,
                    'kategori_risiko' => $kategori,
                    'rekomendasi_perawatan' => $rekomendasi,
                    'jadwal_maintenance_berikutnya' => $jadwalBerikutnya,
                ]);

                $penanggungJawab = $this->getPenanggungJawabAset($validated['kode_aset']);

                $nomorWhatsapp = null;
                if ($penanggungJawab) {
                    $nomorWhatsapp = User::where('name', $penanggungJawab)->value('nomor_whatsapp');
                }

                JadwalMaintenance::updateOrCreate(
                    ['analisis_rcm_id' => $fmeaAnalisis->id],
                    [
                        'kode_aset' => $validated['kode_aset'],
                        'kode_komponen' => $validated['kode_komponen'],
                        'tanggal_maintenance' => $jadwalBerikutnya,
                        'prioritas' => $kategori,
                        'status_jadwal' => 'Dijadwalkan',
                        'tindakan_maintenance' => $rekomendasi,
                        'catatan_tambahan' => null,
                        'penanggungjawab' => $penanggungJawab,
                        'nomor_whatsapp' => $nomorWhatsapp,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Analisis risiko berhasil diperbarui.',
                    'data' => $fmeaAnalisis->fresh()->load(['aset', 'komponenRel', 'jadwalMaintenance'])
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui analisis risiko: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(FmeaAnalisis $fmeaAnalisis)
    {
        try {
            DB::transaction(function () use ($fmeaAnalisis) {
                JadwalMaintenance::where('analisis_rcm_id', $fmeaAnalisis->id)->delete();
                $fmeaAnalisis->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Analisis risiko dan jadwal maintenance terkait berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus analisis risiko: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(FmeaAnalisis $fmeaAnalisis)
    {
        return response()->json([
            'success' => true,
            'data' => $fmeaAnalisis->load(['aset', 'komponenRel'])
        ]);
    }
}