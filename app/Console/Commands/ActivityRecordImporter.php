<?php

namespace App\Console\Commands;

use App\Console\Services\BatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActivityRecordImporter extends Command
{
    private const DATA_TABLE = 'import_format_example';

    protected $signature = 'app:import-activity-records';
    protected $description = 'Used to import activity records with pre-allocated ID batching';

    protected BatchService $batchService;

    public function __construct(BatchService $batchService)
    {
        parent::__construct();
        $this->batchService = $batchService;
    }

    public function handle(): int
    {
        try {
            $emptyShift = DB::table('datahub_attendance_shifts')
                ->where('duration', 0)
                ->first();

            if (!$emptyShift) {
                $this->error('Empty shift with duration = 0 was not found.');
                return Command::FAILURE;
            }

            $emptyShiftId = $emptyShift->id;

            $this->info('Fetching and grouping records from ' . self::DATA_TABLE . '...');

            $groupedRecords = DB::table(self::DATA_TABLE)
                ->select(
                    'operation_id',
                    'date',
                    'employee_contract_id',
                    'real_duration',
                    'shareable_group',
                    'maintainable_type',
                    'maintainable_id',
                    'quantity'
                )
                // ->limit(200)
                ->get()
                ->groupBy('shareable_group')
                ->values();

            $flattenedRecords = $groupedRecords->flatten();
            $operationIds = $flattenedRecords->pluck('operation_id')->unique();
            $contractIds = $flattenedRecords->pluck('employee_contract_id')->unique();

            $contracts = DB::table('datahub_employee_contracts')
                ->whereIn('id', $contractIds)
                ->get([
                    'id',
                    'pid',
                    'datahub_employee_id',
                    'datahub_department_id',
                ])
                ->keyBy('id');

            $employeeIds = $contracts->pluck('datahub_employee_id')->unique();

            $employees = DB::table('datahub_employees')
                ->whereIn('id', $employeeIds)
                ->get([
                    'id',
                    'first_name',
                    'last_name'
                ])
                ->keyBy('id');

            $operations = DB::table('dpb_worktimefund_model_operation')
                ->whereIn('id', $operationIds)
                ->get()
                ->keyBy('id');

            $existingWorktimes = DB::table('dpb_worktimefund_model_worktime')
                ->select('id', 'personal_id', 'date', 'department')
                ->get()
                ->keyBy(fn($item) => "{$item->date}_{$item->personal_id}_{$item->department}");

            $contextId = $this->batchService->getOrCreateBatchContext(
                'ActivityRecord_Import',
                'Import activity records and tasks'
            );
            $batchId = $this->batchService->createBatch($contextId);

            $nextIds = $this->preAllocateIds();

            $worktimeData = [];
            $workTasksData = [];
            $activityRecordsData = [];
            $batchRecords = [];

            $seenWorktimes = [];

            $now = now();

            foreach ($groupedRecords as $group) {
                $firstRecord = $group->first();

                $taskId = null;

                foreach ($group as $record) {
                    $contract = $contracts->get($record->employee_contract_id);
                    $employee = $employees->get($contract->datahub_employee_id);
                    $operation = $operations->get($record->operation_id);

                    if (!$contract || !$employee || !$operation) {
                        $this->warn("Missing dependency for record operation_id: {$record->operation_id}");
                        continue;
                    }

                    $departmentId = $contract->datahub_department_id;
                    $workDate = $record->date;
                    $lookupKey = "{$workDate}_{$contract->pid}_{$departmentId}";

                    if (isset($seenWorktimes[$lookupKey])) {
                        $worktimeId = $seenWorktimes[$lookupKey];
                    } elseif ($existingWorktimes->has($lookupKey)) {
                        $worktimeId = $existingWorktimes->get($lookupKey)->id;
                        $seenWorktimes[$lookupKey] = $worktimeId;
                    } else {
                        $worktimeId = $nextIds['dpb_worktimefund_model_worktime']++;
                        $seenWorktimes[$lookupKey] = $worktimeId;

                        $worktimeData[] = [
                            'id' => $worktimeId,
                            'date' => $workDate,
                            'first_name' => $employee->first_name,
                            'last_name' => $employee->last_name,
                            'personal_id' => $contract->pid,
                            'shift' => $emptyShiftId,
                            'shift_start' => '00:00:00',
                            'shift_duration' => 0,
                            'department' => $departmentId,
                            'created_at' => $workDate,
                            'updated_at' => $workDate,
                        ];

                        $batchRecords[] = [
                            'batch_id' => $batchId,
                            'record_id' => $worktimeId,
                            'record_type' => 'dpb_worktimefund_model_worktime',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if ($taskId === null) {
                        $taskId = $nextIds['dpb_worktimefund_model_task']++;

                        $workTasksData[] = [
                            'id' => $taskId,
                            'source_id' => $operation->id,
                            'title' => $operation->title,
                            'expected_duration' => $operation->duration,
                            'is_shareable' => $operation->is_shareable,
                            'status' => 'started',
                            'department_id' => $departmentId,
                            'created_at' => $now,
                            'updated_at' => $now,
                            'maintainable_type' => $record->maintainable_type,
                            'maintainable_id' => $record->maintainable_id,
                        ];

                        $batchRecords[] = [
                            'batch_id' => $batchId,
                            'record_id' => $taskId,
                            'record_type' => 'dpb_worktimefund_model_task',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    $calculatedDuration = $operation->duration / $group->count() * $record->quantity;
                    $activityId = $nextIds['dpb_worktimefund_model_activityrecord']++;

                    $activityRecordsData[] = [
                        'id' => $activityId,
                        'title' => $operation->title,
                        'type' => 'O',
                        'expected_duration' => $calculatedDuration,
                        'real_duration' => $calculatedDuration,
                        'is_official' => $operation->is_official,
                        'is_fulfilled' => 1,
                        'date' => $record->date,
                        'start' => $record->date,
                        'end' => $record->date,
                        'personal_id' => $contract->pid,
                        'department_id' => $departmentId,
                        'source_id' => $operation->id,
                        'parent_id' => $worktimeId,
                        'task_id' => $taskId,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'quantity' => $record->quantity,
                    ];

                    $batchRecords[] = [
                        'batch_id' => $batchId,
                        'record_id' => $activityId,
                        'record_type' => 'dpb_worktimefund_model_activityrecord',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            $this->moveAutoIncrementOffsets($workTasksData, $activityRecordsData, $worktimeData);

            DB::transaction(function () use ($worktimeData, $workTasksData, $activityRecordsData, $batchRecords) {
                $insertionOrder = [
                    'dpb_worktimefund_model_worktime' => $worktimeData,
                    'dpb_worktimefund_model_task' => $workTasksData,
                    'dpb_worktimefund_model_activityrecord' => $activityRecordsData,
                ];

                foreach ($insertionOrder as $tableName => $data) {
                    if (!empty($data)) {
                        $this->info("Bulk inserting into {$tableName} (" . count($data) . " rows)...");
                        foreach (array_chunk($data, 300) as $chunk) {
                            DB::table($tableName)->insert($chunk);
                        }
                    }
                }

                if (!empty($batchRecords)) {
                    $this->info("Logging batch records (" . count($batchRecords) . " rows)...");
                    foreach (array_chunk($batchRecords, 500) as $chunk) {
                        $this->batchService->logBatchRecordMultiple($chunk);
                    }
                }
            });

            $this->info('Import completed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    private function moveAutoIncrementOffsets(array $workTasksData, array $activityRecordsData, array $worktimeData): void
    {
        if (!empty($workTasksData)) {
            $maxWorkTaskId = $workTasksData ? max(array_column($workTasksData, 'id')) : 0;
            $newAutoIncrement = $maxWorkTaskId + 300; // Add buffer
            DB::statement("ALTER TABLE dpb_worktimefund_model_task AUTO_INCREMENT = " . ($newAutoIncrement));
            $this->info("Table dpb_worktimefund_model_task: max_id={$maxWorkTaskId}, next_id={$newAutoIncrement}, allocated auto_increment offset.");
        }

        if (!empty($activityRecordsData)) {
            $maxActivityId = $activityRecordsData ? max(array_column($activityRecordsData, 'id')) : 0;
            $newAutoIncrement = $maxActivityId + 300; // Add buffer
            DB::statement("ALTER TABLE dpb_worktimefund_model_activityrecord AUTO_INCREMENT = " . ($newAutoIncrement));
            $this->info("Table dpb_worktimefund_model_activityrecord: max_id={$maxActivityId}, next_id={$newAutoIncrement}, allocated auto_increment offset.");
        }

        if (!empty($worktimeData)) {
            $maxWorktimeId = $worktimeData ? max(array_column($worktimeData, 'id')) : 0;
            $newAutoIncrement = $maxWorktimeId + 300;  // Add buffer
            DB::statement("ALTER TABLE dpb_worktimefund_model_worktime AUTO_INCREMENT = " . ($newAutoIncrement));
            $this->info("Table dpb_worktimefund_model_worktime: max_id={$maxWorktimeId}, next_id={$newAutoIncrement}, allocated auto_increment offset.");
        }
    }

    private function preAllocateIds(): array
    {
        $tables = [
            'dpb_worktimefund_model_worktime',
            'dpb_worktimefund_model_task',
            'dpb_worktimefund_model_activityrecord',
        ];

        $nextIds = [];

        foreach ($tables as $table) {
            $maxId = DB::table($table)->max('id') ?? 0;
            $nextId = $maxId + 300; // Add a buffer of 300 to avoid collisions
            $nextIds[$table] = $nextId;
        }

        return $nextIds;
    }
}
