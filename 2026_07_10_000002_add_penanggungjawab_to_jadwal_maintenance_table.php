<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_maintenance', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_maintenance', 'penanggungjawab')) {
                $table->string('penanggungjawab')->nullable()->after('tanggal_maintenance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_maintenance', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_maintenance', 'penanggungjawab')) {
                $table->dropColumn('penanggungjawab');
            }
        });
    }
};
