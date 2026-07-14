<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_maintenance', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_maintenance', 'nomor_whatsapp')) {
                $table->string('nomor_whatsapp')->nullable()->after('penanggungjawab');
            }
        });

        if (Schema::hasColumn('jadwal_maintenance', 'nomor_whatsapp')) {
            DB::statement("UPDATE jadwal_maintenance jm
                LEFT JOIN aset a ON a.kode_aset = jm.kode_aset
                LEFT JOIN users u ON u.name = a.penanggungjawab
                SET jm.nomor_whatsapp = u.nomor_whatsapp
                WHERE jm.nomor_whatsapp IS NULL");
        }
    }

    public function down(): void
    {
        Schema::table('jadwal_maintenance', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_maintenance', 'nomor_whatsapp')) {
                $table->dropColumn('nomor_whatsapp');
            }
        });
    }
};
