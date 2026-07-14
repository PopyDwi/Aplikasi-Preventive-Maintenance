<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            if (!Schema::hasColumn('aset', 'penanggungjawab')) {
                $table->string('penanggungjawab')->nullable()->after('tanggal_instalasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            if (Schema::hasColumn('aset', 'penanggungjawab')) {
                $table->dropColumn('penanggungjawab');
            }
        });
    }
};
