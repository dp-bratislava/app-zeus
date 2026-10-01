<?php

namespace App\Console\Services;

use Illuminate\Support\Facades\DB;

class BatchService
{
    const BATCH_RECORDS_TABLE = 'dpb_batchable_batch_records';
    const BATCH_CONTEXT_TABLE = 'dpb_batchable_batch_contexts';
    const BATCHES_TABLE = 'dpb_batchable_batches';

    public function getOrCreateBatchContext(string $code, string $description): int
    {
        DB::table(self::BATCH_CONTEXT_TABLE)->updateOrInsert(
            ['code' => $code],
            [
                'code' => $code,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return DB::table('dpb_batchable_batch_contexts')
            ->where('code', $code)
            ->value('id');
    }

    /**
     * Create a new batch and return its ID.
     *
     * @param int $contextId
     * @return int Batch ID
     */
    public function createBatch(int $contextId): int
    {
        return DB::table(self::BATCHES_TABLE)->insertGetId(
            [
                'context_id' => $contextId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Log a record to batch records table.
     *
     * @param int $batchId
     * @param int $recordId
     * @param string $recordType
     */
    public function logBatchRecord(int $batchId, int $recordId, string $recordType): void
    {
        DB::table(self::BATCH_RECORDS_TABLE)->insert(
            [
                'batch_id' => $batchId,
                'record_id' => $recordId,
                'record_type' => $recordType,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function logBatchRecordMultiple(array $records): void
    {
        DB::table('dpb_batchable_batch_records')->insert($records);
    }


}