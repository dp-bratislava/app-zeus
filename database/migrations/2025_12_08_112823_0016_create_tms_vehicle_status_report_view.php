<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix');

        DB::statement("
            CREATE VIEW " . $tablePrefix . "vw_vehicle_status_report AS
            SELECT
                de.vehicle_id,
                MIN(de.date) AS `date_from`
            FROM " . $tablePrefix . "daily_expeditions de
            WHERE de.state = 'out-of-service'
              AND NOT EXISTS (
                  SELECT 1
                  FROM " . $tablePrefix . "daily_expeditions de2
                  WHERE de2.vehicle_id = de.vehicle_id
                    AND de2.`date` > de.`date`
                    AND de2.state != 'out-of-service'
              )
            GROUP BY de.vehicle_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix');
        DB::statement("DROP VIEW IF EXISTS " . $tablePrefix . "vw_vehicle_status_report");
    }
};
