<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Console\Services\BatchService;

class CreateMissingAsphereOperations extends Command{

    private BatchService $batchService;

    private $tables;
    private $batchableBatchRecordsTable;
    private $operationsTable;
    private $tempOperationIdColumnForeign;
    private $tempOperationTitleColumn;
    private $tempDurationColumn;
    private $categoryTitle;
    private $roundToMinutes;

    protected $signature = 'app:create-missing-asphere-operations
                            {--tables=combined_table_kontroly,combined_table_poruchy : Comma-separated table names to process}
                            {--batch-records-table=tmp_asphere_import_batchable_batch_records : Temporary batch records table name}
                            {--operations-table=dpb_worktimefund_model_operation : Operations table name}
                            {--operation-id-column=operation_id : Operation ID column name}
                            {--operation-title-column=Štandard : Operation title column name}
                            {--duration-column=Čas štandardu [hod] : Duration column name}
                            {--category-title=Asphere Imported Operations : Category title for imported operations}
                            {--round-to-minutes : Round duration to nearest minute}';

    protected $description = 'create missing operations';

    public function __construct(BatchService $batchService)
    {
        parent::__construct();
        $this->batchService = $batchService;
    }

    public function handle()
    {
        // Load configuration from options with defaults
        $this->tables = explode(',', $this->option('tables'));
        $this->batchableBatchRecordsTable = $this->option('batch-records-table');
        $this->operationsTable = $this->option('operations-table');
        $this->tempOperationIdColumnForeign = $this->option('operation-id-column');
        $this->tempOperationTitleColumn = $this->option('operation-title-column');
        $this->tempDurationColumn = $this->option('duration-column');
        $this->categoryTitle = $this->option('category-title');
        $this->roundToMinutes = $this->option('round-to-minutes');

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
                
                if ($this->roundToMinutes) {
                    $duration = round($duration / 60) * 60;
                }
                
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
                    $uniqueKey = $title . '|' . $duration;
                    if (!isset($uniqueOperationKeys[$uniqueKey])) {
                        $this->info("No matching operation found for record ID {$record->id} with title '{$record->{$this->tempOperationTitleColumn}}' and duration {$duration} seconds.");
                        $uniqueOperationKeys[$uniqueKey] = true;
                        $asphereOperationsToBeCreated[] = [
                            'title' => $title,
                            'duration' => $duration
                        ];
                    }
                }
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
            ->where('title', $this->categoryTitle)
            ->value('id');

        // 2. If it doesn't exist, insert it and capture the new ID
        if (!$asphereCategoryId) {
            $asphereCategoryId = DB::table('dpb_worktimefund_model_category')->insertGetId([
                'title' => $this->categoryTitle,
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