<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Console\Services\BatchService;

class CreateMissingAsphereOperations extends Command{

    private BatchService $batchService;
    // private $tables = ['Temporary_Kontroly_autobusy', 'Temporary_Poruchy_autobusy','Temporary_poruchy_elektrika','Temporary_kontroly_elektrika'];
    private $tables = ['combined_table_kontroly', 'combined_table_poruchy'];

    private $batchableBatchRecordsTable = 'tmp_asphere_import_batchable_batch_records';

    private $operationsTable = 'dpb_worktimefund_model_operation';
    private $tempOperationIdColumnForeign = 'operation_id';
    private $tempOperationTitleColumn = 'Štandard';

    private $tempDurationColumn = 'Čas štandardu [hod]';

    protected $signature = 'app:create-missing-asphere-operations';

    protected $description = 'create missing operations';

    public function __construct(BatchService $batchService)
    {
        parent::__construct();
        $this->batchService = $batchService;
    }

    public function handle()
    {
        if(!Schema::hasTable($this->batchableBatchRecordsTable)){
            $this->info("Record log table not found. Attempting to create table {$this->batchableBatchRecordsTable} for batch record logging...");
            $this->createTmpBatchRecordsTable();
        }

        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                $this->error("Table $table does not exist, no further proccessing done");
                return;
            }

            $this->info('Matching Operations for table ' . $table . '...');
            $asphereOperationsToBeCreated = $this->syncOperations($table);
            if(!empty($asphereOperationsToBeCreated)) {
                $this->createAsphereOperations($asphereOperationsToBeCreated);
                $this->syncOperations($table);
            }
        }
    }


    private function syncOperations($table): array{
        $records = DB::table($table)
                ->get();
            $asphereOperationsToBeCreated = [];
            $uniqueOperationKeys = [];
            foreach ($records as $record) {
                $hoursValue = str_replace(',', '.', $record->{$this->tempDurationColumn});
                $duration = (float)$hoursValue * 3600;
                $title = $record->{$this->tempOperationTitleColumn};
                
                $operation = DB::table($this->operationsTable)
                    ->where(function ($query) use ($record) {
                        $query->where('title', '=', $record->{$this->tempOperationTitleColumn});
                    })
                    ->where('duration', $duration)
                    ->first();
                
                if ($operation) {
                    DB::table($table)
                        ->where('id', $record->id)
                        ->update([$this->tempOperationIdColumnForeign => $operation->id]);
                }
                else {
                    $this->info("No matching operation found for record ID {$record->id} with title '{$record->{$this->tempOperationTitleColumn}}' and duration {$duration} seconds.");
                    $uniqueKey = $title . '|' . $duration;
                    if (!isset($uniqueOperationKeys[$uniqueKey])) {
                        $uniqueOperationKeys[$uniqueKey] = true;
                        $asphereOperationsToBeCreated[] = [
                            'title' => $title,
                            'duration' => $duration
                        ];
                    }
                }
            }
            if(count($records) * 0.8 < count($asphereOperationsToBeCreated)){
                $this->warn("ERROR NO FURTHER ACTION> A large number of records are missing operations. Missing operations count: " . count($asphereOperationsToBeCreated) . " out of " . count($records) . " total records. This might indicate a data issue or mismatch in operation criteria.");
                return [];
            }
            return $asphereOperationsToBeCreated;
    }

    private function createAsphereOperations(array $operations): void
    {
        $this->info('creating asphere operations.');

        $contextId = $this->batchService->getOrCreateBatchContext(
            'Asphere_Operation_Creation',
            'adding missing operations from Asphere that we need for import to work'
        );

        $batchId = $this->batchService->createBatch($contextId);

        
        // create the category
        // 1. Try to get the ID from an existing record
        $asphereCategoryId = DB::table('dpb_worktimefund_model_category')
            ->where('title', 'Asphere Imported Operations')
            ->value('id');

        // 2. If it doesn't exist, insert it and capture the new ID
        if (!$asphereCategoryId) {
            $asphereCategoryId = DB::table('dpb_worktimefund_model_category')->insertGetId([
                'title' => 'Asphere Imported Operations',
                'type' => 'default',
                'created_at' => now(),
                'updated_at' => now(),
                'is_official' => 1,
            ]);
            $this->batchService->logBatchRecord($batchId, $asphereCategoryId, 'dpb_worktimefund_model_category');
        }

        $operationToSaveAsBatch= [];
        foreach ($operations as $operationData) {
            $newOperationId = DB::table('dpb_worktimefund_model_operation')->insertGetId(
                [
                    'title' => $operationData['title'],
                    'duration' => $operationData['duration'],
                    'parent_id' => $asphereCategoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'is_official' => 1,
                    'is_shareable' => 0,
                ]
            );
            $operationToSaveAsBatch[] = [
                'batch_id' => $batchId,
                'record_id' => $newOperationId,
                'record_type' => 'dpb_worktimefund_model_operation',
            ];
            // Log the creation in dpb_batchable_batch_records
            // $this->batchService->logBatchRecord($batchId, $newOperationId, 'dpb_worktimefund_model_operation');
        }
        $this->batchService->logBatchRecordMultiple($operationToSaveAsBatch);
    }
    private function createTmpBatchRecordsTable(): void
    {

        if (!Schema::hasTable($this->batchableBatchRecordsTable)) {
            DB::statement("
                CREATE TABLE {$this->batchableBatchRecordsTable} (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    batch_id BIGINT UNSIGNED NOT NULL,
                    record_id BIGINT UNSIGNED NOT NULL,
                    record_type VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_batch_id (batch_id),
                    INDEX idx_record_id (record_id),
                    CONSTRAINT fk_batch_id FOREIGN KEY (batch_id) 
                        REFERENCES dpb_batchable_batches(id) ON DELETE CASCADE
                )
            ");
            $this->info("Table {$this->batchableBatchRecordsTable} created successfully.");
        } else {
            $this->info("Table {$this->batchableBatchRecordsTable} already exists.");
        }
    }
}