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
        Schema::table('riwayat_pekerjaan', function (Blueprint $table) {
            if (!Schema::hasColumn('riwayat_pekerjaan', 'kode_aset')) {
                $table->string('kode_aset', 50)->nullable()->after('jadwal_maintenance_id');
            }
            if (!Schema::hasColumn('riwayat_pekerjaan', 'kode_komponen')) {
                $table->string('kode_komponen', 50)->nullable()->after('kode_aset');
            }
            if (!Schema::hasColumn('riwayat_pekerjaan', 'hasil_pekerjaan')) {
                $table->longText('hasil_pekerjaan')->nullable()->after('hasil_pengecekan');
            }
            if (!Schema::hasColumn('riwayat_pekerjaan', 'catatan_teknisi')) {
                $table->longText('catatan_teknisi')->nullable()->after('tindakan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_pekerjaan', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_pekerjaan', 'catatan_teknisi')) {
                $table->dropColumn('catatan_teknisi');
            }
            if (Schema::hasColumn('riwayat_pekerjaan', 'hasil_pekerjaan')) {
                $table->dropColumn('hasil_pekerjaan');
            }
            if (Schema::hasColumn('riwayat_pekerjaan', 'kode_komponen')) {
                $table->dropColumn('kode_komponen');
            }
            if (Schema::hasColumn('riwayat_pekerjaan', 'kode_aset')) {
                $table->dropColumn('kode_aset');
            }
        });
    }
};
