<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_pekerjaan', function (Blueprint $table) {
            if (!Schema::hasColumn('riwayat_pekerjaan', 'penanggungjawab')) {
                $table->string('penanggungjawab')->nullable()->after('kode_komponen');
            }
            if (!Schema::hasColumn('riwayat_pekerjaan', 'nomor_whatsapp')) {
                $table->string('nomor_whatsapp')->nullable()->after('penanggungjawab');
            }
        });

        if (Schema::hasColumn('riwayat_pekerjaan', 'penanggungjawab') && Schema::hasColumn('riwayat_pekerjaan', 'nomor_whatsapp')) {
            DB::statement("UPDATE riwayat_pekerjaan r
                LEFT JOIN jadwal_maintenance jm ON jm.id = r.jadwal_maintenance_id
                SET r.penanggungjawab = jm.penanggungjawab,
                    r.nomor_whatsapp = jm.nomor_whatsapp
                WHERE r.penanggungjawab IS NULL OR r.nomor_whatsapp IS NULL");
        }
    }

    public function down(): void
    {
        Schema::table('riwayat_pekerjaan', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_pekerjaan', 'nomor_whatsapp')) {
                $table->dropColumn('nomor_whatsapp');
            }
            if (Schema::hasColumn('riwayat_pekerjaan', 'penanggungjawab')) {
                $table->dropColumn('penanggungjawab');
            }
        });
    }
};
