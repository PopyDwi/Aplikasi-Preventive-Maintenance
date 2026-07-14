<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komponen', function (Blueprint $table) {
            $table->id();
            $table->string('kode_komponen', 10)->unique();
            $table->string('kode_aset', 10);
            $table->string('nama_komponen', 150);
            $table->text('fungsi_keterangan')->nullable();
            $table->integer('volume')->default(0);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('kode_aset')
                ->references('kode_aset')
                ->on('asets')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komponens');
    }
};
