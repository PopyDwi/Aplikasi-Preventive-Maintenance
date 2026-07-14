<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fmea_analisis', function (Blueprint $table) {
            $table->id();
            
            // Data Mesin dan Komponen
            $table->string('kode_mesin', 50);
            $table->string('komponen', 100);
            $table->string('mode_kegagalan', 200);
            
            // Nilai FMEA
            $table->integer('severity'); // 1-10
            $table->integer('occurrence'); // 1-10
            $table->integer('detection'); // 1-10
            
            // Hasil Perhitungan
            $table->integer('rpn'); // RPN = S x O x D
            $table->string('kategori_risiko'); // Sangat Tinggi, Tinggi, Sedang, Rendah
            $table->string('interval_maintenance'); // Setiap X hari
            
            // Deskripsi
            $table->longText('dampak_kegagalan')->nullable();
            $table->longText('rekomendasi_perawatan');
            
            // Jadwal
            $table->date('tanggal_jadwal_berikutnya');
            $table->timestamp('tanggal_input')->useCurrent();
            
            // Timestamps
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('kode_mesin');
            $table->index('kategori_risiko');
            $table->index('rpn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fmea_analisis');
    }
};
