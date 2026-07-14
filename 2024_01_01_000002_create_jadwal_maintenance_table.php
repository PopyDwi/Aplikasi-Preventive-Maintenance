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
        Schema::create('jadwal_maintenance', function (Blueprint $table) {
            $table->id();
            
            // Data Mesin dan Komponen
            $table->string('kode_mesin', 50);
            $table->string('komponen', 100);
            $table->string('mode_kegagalan', 200)->nullable();
            
            // Data dari Analisis FMEA
            $table->string('kategori_risiko', 50)->nullable();
            $table->integer('rpn')->nullable();
            
            // Jadwal dan penanggung jawab
            $table->date('tanggal_maintenance');
            $table->string('penanggungjawab')->nullable();
            
            // Prioritas dan Status
            $table->enum('prioritas', ['Sangat Tinggi', 'Tinggi', 'Sedang', 'Rendah']);
            $table->enum('status', ['Dijadwalkan', 'Pending', 'Proses', 'Selesai'])->default('Dijadwalkan');
            
            // Tindakan dan Catatan
            $table->longText('tindakan_maintenance');
            $table->longText('catatan_tambahan')->nullable();
            
            // Relasi ke FMEA Analisis (opsional)
            $table->unsignedBigInteger('fmea_analisis_id')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('fmea_analisis_id')
                  ->references('id')
                  ->on('fmea_analisis')
                  ->onDelete('set null');
            
            // Index untuk performa query
            $table->index('kode_mesin');
            $table->index('tanggal_maintenance');
            $table->index('status');
            $table->index('prioritas');
            $table->index('penanggungjawab');
            $table->index('fmea_analisis_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_maintenance');
    }
};
