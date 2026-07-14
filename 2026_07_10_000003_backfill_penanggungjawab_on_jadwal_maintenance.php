<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $columns = DB::connection()->getSchemaBuilder()->getColumnListing('jadwal_maintenance');

        if (in_array('penanggungjawab', $columns, true)) {
            $updateSql = "UPDATE jadwal_maintenance jm
                LEFT JOIN aset a ON a.kode_aset = jm.kode_aset
                SET jm.penanggungjawab = a.penanggungjawab
                WHERE jm.penanggungjawab IS NULL";

            DB::statement($updateSql);
        }
    }

    public function down(): void
    {
        // Tidak ada rollback data karena ini hanya mengisi data.
    }
};
