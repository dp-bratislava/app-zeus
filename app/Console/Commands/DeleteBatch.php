<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteBatch extends Command
{
    const BATCH_RECORDS_TABLE = 'dpb_batchable_batch_records';
    const BATCH_CONTEXT_TABLE = 'dpb_batchable_batch_contexts';
    const BATCHES_TABLE = 'dpb_batchable_batches';

    const CHUNK_SIZE = 5000;

    protected $signature = 'app:delete-batch {batchId : The ID of the batch to delete}';

    protected $description = 'Hard delete all records belonging to a batch in scalable chunks';

    public function handle()
    {
        $batchId = $this->argument('batchId');

        if (!$this->confirm("Are you sure you want to hard delete all records in batch {$batchId}?")) {
            $this->info('Deletion cancelled.');
            return;
        }

        try {
            // Count distinct record types present in this batch directly in SQL
            $recordTypesInBatch = DB::table(self::BATCH_RECORDS_TABLE)
                ->where('batch_id', $batchId)
                ->distinct()
                ->pluck('record_type')
                ->toArray();

            if (empty($recordTypesInBatch)) {
                $this->warn("No records found for batch {$batchId}.");
                return;
            }

            $deletionOrder = [
                'tms_task_item_assignments',
                'tms_task_assignments',
                'tms_inspection_assignments',
                'tsk_task_items',
                'tsk_task_item_groups',
                'tsk_tasks',
                'insp_inspections',
                'dpb_worktimefund_model_activityrecord',
                'dpb_worktimefund_model_task',
                'dpb_worktimefund_model_operation',
            ];

            // Reorder target tables: explicit order first, followed by any unlisted record types
            $orderedTypes = array_intersect($deletionOrder, $recordTypesInBatch);
            $remainingTypes = array_diff($recordTypesInBatch, $deletionOrder);
            $allTypesToDelete = array_merge($orderedTypes, $remainingTypes);

            $totalDeleted = 0;

            foreach ($allTypesToDelete as $recordType) {
                $typeDeleted = $this->deleteRecordsForType($batchId, $recordType);
                $totalDeleted += $typeDeleted;
                
                if ($typeDeleted > 0) {
                    $this->info("Deleted total {$typeDeleted} records from {$recordType}");
                }
            }

            // Delete batch records in chunks
            $this->deleteBatchRecordsInChunks($batchId);

            // Delete the main batch record
            DB::table(self::BATCHES_TABLE)
                ->where('id', $batchId)
                ->delete();

            $this->info("Batch {$batchId} and all its {$totalDeleted} associated records have been successfully deleted!");

        } catch (\Exception $e) {
            $this->error('Error during deletion execution: ' . $e->getMessage());
        }
    }

    /**
     * Delete target records linked via batch_records using subquery chunks.
     */
    private function deleteRecordsForType(string|int $batchId, string $targetTable): int
    {
        $totalDeleted = 0;

        do {
            // Fetch a chunk of primary IDs from the batch record pivot table
            $idsToDelete = DB::table(self::BATCH_RECORDS_TABLE)
                ->where('batch_id', $batchId)
                ->where('record_type', $targetTable)
                ->limit(self::CHUNK_SIZE)
                ->pluck('record_id')
                ->toArray();

            if (empty($idsToDelete)) {
                break;
            }

            // Execute chunked deletion without hitting SQL parameter limit ceilings
            $deleted = DB::table($targetTable)
                ->whereIn('id', $idsToDelete)
                ->delete();

            // Remove the processed batch record entries to advance the chunk offset
            DB::table(self::BATCH_RECORDS_TABLE)
                ->where('batch_id', $batchId)
                ->where('record_type', $targetTable)
                ->whereIn('record_id', $idsToDelete)
                ->delete();

            $totalDeleted += $deleted;

        } while (count($idsToDelete) === self::CHUNK_SIZE);

        return $totalDeleted;
    }

    /**
     * Clean up any remaining batch records in chunks.
     */
    private function deleteBatchRecordsInChunks(string|int $batchId): void
    {
        do {
            $deleted = DB::table(self::BATCH_RECORDS_TABLE)
                ->where('batch_id', $batchId)
                ->limit(self::CHUNK_SIZE)
                ->delete();
        } while ($deleted > 0);
    }
}