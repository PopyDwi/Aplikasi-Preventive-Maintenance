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
        // Hanya update status enum dengan nilai baru
        Schema::table('jadwal_maintenance', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('jadwal_maintenance', function (Blueprint $table) {
            $table->enum('status', ['Dijadwalkan', 'Diproses', 'Selesai', 'Dibatalkan'])
                  ->default('Dijadwalkan')
                  ->after('prioritas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_maintenance', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('jadwal_maintenance', function (Blueprint $table) {
            $table->enum('status', ['Dijadwalkan', 'Pending', 'Proses', 'Selesai'])
                  ->default('Dijadwalkan');
        });
    }
};
