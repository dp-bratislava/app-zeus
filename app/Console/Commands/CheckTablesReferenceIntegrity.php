<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CheckTablesReferenceIntegrity extends Command{

    // private $tables = ['Temporary_Kontroly_autobusy', 'Temporary_Poruchy_autobusy','Temporary_poruchy_elektrika','Temporary_kontroly_elektrika'];
    private $tables = ['combined_table_kontroly', 'combined_table_poruchy'];
    private $columnMapping = [
    'employee_contract_id' => ['datahub_employee_contracts', 'id'],
    'department_id' => ['datahub_departments', 'id'],
    'vehicle_id' => ['fleet_vehicles', 'id'],
    'operation_id' => ['dpb_worktimefund_model_operation', 'id'],
    'task_item_group_id' => ['tsk_task_item_groups', 'id'],
    'author_id' => ['users', 'id'],
    'inspection_template_id' => ['insp_inspection_templates', 'id'],
];

    protected $signature = 'app:check-tables-reference-integrity';

    protected $description = 'check whether ids point to real records in other tables';

    public function handle()
    {
        foreach ($this->tables as $table) {
            $this->info("Checking table: {$table}");
            foreach ($this->columnMapping as $column => [$referencedTable, $referencedColumn]) {
                if (Schema::hasColumn($table, $column)) {
                    $missingReferences = DB::table($table)
                        ->leftJoin($referencedTable, "{$table}.{$column}", '=', "{$referencedTable}.{$referencedColumn}")
                        ->whereNull("{$referencedTable}.{$referencedColumn}")
                        ->select("{$table}.id", "{$table}.{$column}")
                        ->get();

                    if ($missingReferences->isEmpty()) {
                        $this->info("  All references in column '{$column}' are valid.");
                    } else {
                        $this->error("  Found " . $missingReferences->count() . " missing references in column '{$column}':");
                        foreach ($missingReferences as $reference) {
                            $this->line("    Record ID: {$reference->id}, Missing Reference ID: {$reference->{$column}}");
                        }
                    }
                } else {
                    $this->warn("  Column '{$column}' does not exist in table '{$table}'. Skipping reference check for this column.");
                }
            }
        }

        return 0;
    }

}