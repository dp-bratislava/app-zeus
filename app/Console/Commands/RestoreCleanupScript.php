<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestoreCleanupScript extends Command
{

    protected $signature = 'restore:cleanup-script {--cleanup : Drop the TEMP tables after successful restoration}';

    protected $description = 'Restore original state of activity records and work tasks from TEMP tables';
    public function handle(): int
    {
        $this->info('Starting data restoration process...');

        // Verify that backup tables exist before proceeding
        $requiredTables = [
            'TEMP_work_tasks_removed',
            'TEMP_mm_workorder_task_removed',
            'TEMP_activity_record_updates',
            'TEMP_work_task_updates'
        ];

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->error("Required backup table [{$table}] is missing. Cannot proceed with full restoration.");
                return 1;
            }
        }

        try {
            DB::transaction(function () {
                // 1. Restore completely deleted work tasks (Satisfies Foreign Key dependencies)
                $this->line('Restoring deleted work tasks...');
                $deletedTasks = DB::table('TEMP_work_tasks_removed')->get();
                foreach ($deletedTasks as $task) {
                    DB::table('dpb_worktimefund_model_task')->insertOrIgnore((array) $task);
                }

                // 2. Restore pivot table relationships
                $this->line('Restoring work order task pivot relationships...');
                $deletedPivots = DB::table('TEMP_mm_workorder_task_removed')->get();
                foreach ($deletedPivots as $pivot) {
                    DB::table('dpb_wtftmsbridge_mm_workorder_task')->insertOrIgnore([
                        'workorder_id' => $pivot->workorder_id,
                        'taskitem_id'  => $pivot->taskitem_id,
                    ]);
                }

                // 3. Revert altered activity records back to their original task IDs
                $this->line('Reverting activity record task associations...');
                DB::statement('
                    UPDATE dpb_worktimefund_model_activityrecord ar
                    JOIN TEMP_activity_record_updates taru ON ar.id = taru.id
                    SET ar.task_id = taru.old_task_id
                ');

                // 4. Revert modified work tasks back to their original expected durations
                $this->line('Reverting work task expected durations...');
                DB::statement('
                    UPDATE dpb_worktimefund_model_task wt
                    JOIN TEMP_work_task_updates twtu ON wt.id = twtu.id
                    SET wt.expected_duration = twtu.old_duration
                ');
            });

            $this->info('Restoration completed successfully.');

            // Optional cleanup phase
            if ($this->option('cleanup')) {
                $this->line('Cleaning up temporary tables...');
                foreach ($requiredTables as $table) {
                    Schema::dropIfExists($table);
                }
                $this->info('Temporary tables dropped.');
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('An error occurred during restoration. Transaction rolled back.');
            $this->error($e->getMessage());
            return 1;
        }
    }
}