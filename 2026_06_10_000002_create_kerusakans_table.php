<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kerusakan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_kerusakan');
            $table->string('kode_aset', 50);
            $table->string('kode_komponen', 50);
            $table->string('jenis_kerusakan', 255);
            $table->text('deskripsi_kerusakan')->nullable();
            $table->decimal('downtime_jam', 8, 2)->default(0);
            $table->string('teknisi_pelapor', 150)->nullable();
            $table->integer('user_id')->nullable()->index();
            $table->string('status', 50)->default('Belum Ditangani');
            $table->decimal('estimasi_biaya', 14, 2)->default(0);
            $table->text('catatan_teknisi')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('kode_aset');
            $table->index('kode_komponen');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kerusakan');
    }
};
