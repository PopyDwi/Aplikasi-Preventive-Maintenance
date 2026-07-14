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
        Schema::create('riwayat_pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_maintenance_id')->nullable();
            $table->date('tanggal_pekerjaan')->useCurrent();
            $table->string('teknisi', 100)->nullable();
            $table->string('status', 50);
            $table->longText('hasil_pengecekan')->nullable();
            $table->longText('tindakan')->nullable();
            $table->decimal('durasi_jam', 8, 2)->nullable();
            $table->decimal('biaya', 14, 2)->nullable();
            $table->string('dokumentasi')->nullable();
            $table->timestamps();

            $table->foreign('jadwal_maintenance_id')
                ->references('id')->on('jadwal_maintenance')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pekerjaan');
    }
};
