<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-batch {batchId : The ID of the batch to delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hard delete all records belonging to a batch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchId = $this->argument('batchId');

        // Confirm before deletion
        if (!$this->confirm("Are you sure you want to hard delete all records in batch {$batchId}?")) {
            $this->info('Deletion cancelled.');
            return;
        }

        try {
            // Get all records in the batch grouped by type
            $batchRecords = DB::table('tmp_asphere_import_batchable_batch_records')
                ->where('batch_id', $batchId)
                ->get();

            if ($batchRecords->isEmpty()) {
                $this->warn("No records found for batch {$batchId}.");
                return;
            }

            $this->info("Found {$batchRecords->count()} records in batch {$batchId}.");

            // Group records by table type for bulk deletion
            $recordsByType = $batchRecords->groupBy('record_type');
            $totalDeleted = 0;

            // Define deletion order to respect foreign key constraints
            $deletionOrder = [
                'tms_task_item_assignments',  // Must delete task item assignments before task items
                'tms_task_assignments',       // Must delete task assignments before tasks
                'tms_inspection_assignments', // Must delete inspection assignments before inspections
                'tsk_task_items',             // Must delete task items before tasks
                'tsk_task_item_groups',       // Must delete task item groups before tasks
                'tsk_tasks',                  // Delete tasks after their dependent records
                'insp_inspections',           // Delete inspections after their dependent records
                'dpb_worktimefund_model_operation',
            ];

            // First delete in the correct order
            foreach ($deletionOrder as $recordType) {
                if (!isset($recordsByType[$recordType])) {
                    continue;
                }

                $records = $recordsByType[$recordType];
                $recordIds = $records->pluck('record_id')->toArray();
                
                $deleted = DB::table($recordType)
                    ->whereIn('id', $recordIds)
                    ->delete();

                $this->info("Deleted {$deleted} records from {$recordType}");
                $totalDeleted += $deleted;
                unset($recordsByType[$recordType]);
            }

            // Delete any remaining record types not in the predefined order
            foreach ($recordsByType as $recordType => $records) {
                $recordIds = $records->pluck('record_id')->toArray();
                
                try {
                    $deleted = DB::table($recordType)
                        ->whereIn('id', $recordIds)
                        ->delete();

                    $this->info("Deleted {$deleted} records from {$recordType}");
                    $totalDeleted += $deleted;
                } catch (\Exception $e) {
                    $this->warn("Failed to delete records from {$recordType}: " . $e->getMessage());
                }
            }

            // Delete the batch records themselves
            DB::table('tmp_asphere_import_batchable_batch_records')
                ->where('batch_id', $batchId)
                ->delete();

            // Delete the batch
            DB::table('dpb_batchable_batches')
                ->where('id', $batchId)
                ->delete();

            $this->info("Batch {$batchId} and all its {$totalDeleted} records have been successfully deleted!");

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

}
