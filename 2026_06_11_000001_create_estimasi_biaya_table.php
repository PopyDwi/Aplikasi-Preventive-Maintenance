<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimasi_biaya', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kode_aset', 50);
            $table->string('kode_komponen', 50)->nullable();
            $table->decimal('total_downtime', 8, 2)->default(0);
            $table->decimal('biaya_per_jam', 14, 2)->default(0);
            $table->decimal('biaya_perbaikan', 14, 2)->default(0);
            $table->decimal('total_estimasi', 14, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('kode_aset');
            $table->index('kode_komponen');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimasi_biaya');
    }
};
