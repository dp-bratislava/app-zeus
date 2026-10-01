<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FirstTransform9486 extends Command
{
    private const TABLE_FIRST = '7mesiac';
    private const TABLE_SECOND = '8mesiac';
    private const TABLE_THIRD = '9mesiac';
    private const TABLE_TARGET = '9486_combined';


    protected $signature = 'app:9486-first-transform';

    protected $description = 'Creates a combined table from two source tables if it does not already exist.';

    public function handle(): int
    {
        // drop the target table if it exists
        DB::statement("DROP TABLE IF EXISTS `" . self::TABLE_TARGET . "`");

        $table1 = self::TABLE_FIRST;
        $table2 = self::TABLE_SECOND;
        $table3 = self::TABLE_THIRD;
        $targetTable = self::TABLE_TARGET;

        $sql = "CREATE TABLE IF NOT EXISTS `{$targetTable}` AS 
                SELECT * FROM `{$table1}`
                UNION ALL
                SELECT * FROM `{$table2}`
                UNION ALL
                SELECT * FROM `{$table3}`";

        DB::statement($sql);

        // change the collation to match zeus tables
        DB::statement("ALTER TABLE `{$targetTable}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // change Číslo vozidla into VARCHAR(255)
        $alterSql = "ALTER TABLE `{$targetTable}` MODIFY COLUMN `Čislo vozidla` VARCHAR(255) NULL";
        DB::statement($alterSql);

        // now create connecting columns for the target table
        $columns = [
        'vehicle_length' => 'VARCHAR(255)', 
        'vehicle_model_id' => 'BIGINT', 
        'vehicle_model_title' => 'VARCHAR(255)', 
        'vehicle_id' => 'BIGINT', 
        'vehicle_type_id' => 'BIGINT',
        'vehicle_type_title' => 'VARCHAR(255)']; 
        
        foreach ($columns as $column => $type) {
            $alterSql = "ALTER TABLE `{$targetTable}` ADD COLUMN `{$column}` {$type} NULL";
            DB::statement($alterSql);
        }
        
        // Now fill the new columns with data
        $updateSql = "UPDATE `{$targetTable}` t
                        LEFT JOIN `fleet_vehicle_codes` fvc 
                            ON t.`Čislo vozidla` = fvc.code
                        LEFT JOIN (
                            SELECT fvch1.*
                            FROM `fleet_vehicle_code_history` fvch1
                            INNER JOIN (
                                SELECT vehicle_code_id, MAX(id) AS max_id
                                FROM `fleet_vehicle_code_history`
                                GROUP BY vehicle_code_id
                            ) latest 
                            ON fvch1.vehicle_code_id = latest.vehicle_code_id 
                        AND fvch1.id = latest.max_id
                        ) fvch 
                            ON fvc.id = fvch.vehicle_code_id
                        LEFT JOIN `fleet_vehicles` v 
                            ON fvch.vehicle_id = v.id
                        LEFT JOIN `fleet_vehicle_models` vm 
                            ON v.model_id = vm.id
                        LEFT JOIN `fleet_vehicle_types` vt 
                            ON vm.type_id = vt.id
                        SET 
                            t.vehicle_length = vm.length,
                            t.vehicle_model_id = vm.id,
                            t.vehicle_model_title = vm.title,
                            t.vehicle_type_id = vt.id,
                            t.vehicle_type_title = vt.title,
                            t.vehicle_id = v.id";

                        DB::statement($updateSql);

        $this->info("Table `{$targetTable}` processed successfully.");


        return Command::SUCCESS;
    }
}