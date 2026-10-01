<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateImportFormatExample extends Command
{
    private const TABLE_TARGET = 'import_format_example';
    private const SEED_LIMIT = 100;

    protected $signature = 'app:import-format-example';

    protected $description = 'just while developing format for imports';

    public function handle(): int
    {
        // drop the target table if it exists
        DB::statement("DROP TABLE IF EXISTS `" . self::TABLE_TARGET . "`");

        $targetTable = self::TABLE_TARGET;
        $seedLimit = self::SEED_LIMIT;

        $sql = "CREATE TABLE IF NOT EXISTS `{$targetTable}` (
            `operation_id` BIGINT,
            `date` VARCHAR(255) NULL,
            `employee_contract_id` BIGINT,
            `real_duration` BIGINT NULL,
            `maintainable_id` BIGINT NULL,
            `maintainable_type` VARCHAR(255) NULL,
            `shareable_group` CHAR(36) NULL
        )";

        DB::statement($sql);
        
        

        $operations = DB::table('dpb_worktimefund_model_operation')
            ->inRandomOrder()
            ->limit($seedLimit)
            ->get(['id']);
        $contractIds = DB::table('datahub_employee_contracts')
            ->inRandomOrder()
            ->limit($seedLimit)
            ->pluck('id');
        $vehicleIds = DB::table('fleet_vehicles')
            ->inRandomOrder()
            ->limit($seedLimit)
            ->pluck('id');

        $records = [];
        $currentUuid = (string) Str::uuid();

        foreach ($operations as $index => $operation) {
            // Generate a new UUID after every 5 records
            if ($index > 0 && $index % 5 === 0) {
                $currentUuid = (string) Str::uuid();
            }
            $date = now()->toDateString();
            $records[] = [
                'operation_id'         => $operation->id,
                'date'                 => $date,
                'employee_contract_id' => $contractIds[$index % $contractIds->count()] ?? null,
                'real_duration'        => null,
                'maintainable_id'      => $vehicleIds[$index % $vehicleIds->count()] ?? null,
                'maintainable_type'    => 'Dpb\WorkTimeFund\Models\Maintainables\Vehicle',
                'shareable_group'             => $currentUuid,
            ];
        }

        DB::table(self::TABLE_TARGET)->insert($records);

        return Command::SUCCESS;
    }
}