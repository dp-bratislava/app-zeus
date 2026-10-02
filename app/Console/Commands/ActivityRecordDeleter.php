<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActivityRecordDeleter extends Command
{
    const BATCH_RECORDS_TABLE = 'dpb_batchable_batch_records';
    const BATCH_CONTEXT_TABLE = 'dpb_batchable_batch_contexts';
    const BATCHES_TABLE = 'dpb_batchable_batches';

    protected $signature = 'app:delete-activity-records {batchId : The ID of the batch to delete}';

    protected $description = 'Hard delete all records belonging to a batch in scalable chunks';

    public function handle()
    {
        $batchId = $this->argument('batchId');

        if (!$this->confirm("Are you sure you want to hard delete all records in batch {$batchId}?")) {
            $this->info('Deletion cancelled.');
            return;
        }

        try {
            $types = [
                'dpb_worktimefund_model_activityrecord',
                'dpb_worktimefund_model_task',
                'dpb_worktimefund_model_worktime',
            ];

            foreach ($types as $type) {
                $this->deleteRecordsForType($batchId, $type);
                $this->info("Batch {$batchId} : {$type} successfully deleted!");
            }

            DB::table(self::BATCHES_TABLE)
                ->where('id', $batchId)
                ->delete();
                            
            $this->info("Batch {$batchId} successfully deleted!");
        } catch (\Exception $e) {
            $this->error('Error during deletion execution: ' . $e->getMessage());
        }
    }

    private function deleteRecordsForType(string|int $batchId, string $targetTable): int
    {
        return DB::transaction(function () use ($batchId, $targetTable) {
            $deleted = DB::affectingStatement(
                "
            DELETE target
            FROM `{$targetTable}` AS target
            INNER JOIN `" . self::BATCH_RECORDS_TABLE . "` AS br
                ON br.record_id = target.id
            WHERE br.batch_id = ?
              AND br.record_type = ?
            ",
                [$batchId, $targetTable]
            );

            DB::table(self::BATCH_RECORDS_TABLE)
                ->where('batch_id', $batchId)
                ->where('record_type', $targetTable)
                ->delete();

            return $deleted;
        });
    }
}
