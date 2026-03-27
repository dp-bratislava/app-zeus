<?php

namespace App\Console\Commands;

use App\Console\Services\BatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateActivityRecordsAndTasks extends Command
{
    private BatchService $batchService;

    private $sourceTable;
    private $workDateFormat;
    private $workDateColumn;
    private $activityRecordRealTimeColumn;
    private $operationsTable;
    private $contractsTable;
    private $employeesTable;
    private $worktimeFundTable;
    private $tasksTable;
    private $activityRecordsTable;
    private $departmentsTable;
    private $contextName;
    private $contextDescription;

    protected $signature = 'app:create-activity-records-and-tasks
                            {--source-table=zazemne_elektrika : Source table name to process (required)}
                            {--work-date-format=d.m.Y : Date format for work date parsing}
                            {--work-date-column=date : Work date column name}
                            {--duration-column=duration : Duration/hours column name}
                            {--operations-table=dpb_worktimefund_model_operation : Operations table name}
                            {--contracts-table=datahub_employee_contracts : Employee contracts table name}
                            {--employees-table=datahub_employees : Employees table name}
                            {--worktime-table=dpb_worktimefund_model_worktime : Work time table name}
                            {--tasks-table=dpb_worktimefund_model_task : Tasks table name}
                            {--activity-records-table=dpb_worktimefund_model_activityrecord : Activity records table name}
                            {--departments-table=datahub_departments : Departments table name}
                            {--context-name=Activity_Records_Creation : Batch context name}
                            {--context-description=Bulk creation of activity records and tasks : Batch context description}';

    protected $description = 'Create activity records and dpb_worktimefund_model_task records from source data without bridge creation';

    public function __construct(BatchService $batchService)
    {
        parent::__construct();
        $this->batchService = $batchService;
    }

    public function handle()
    {
        // Load configuration from options
        $this->sourceTable = $this->option('source-table');
        
        if (!$this->sourceTable) {
            $this->error('--source-table option is required');
            return 1;
        }

        $this->workDateFormat = $this->option('work-date-format');
        $this->workDateColumn = $this->option('work-date-column');
        $this->activityRecordRealTimeColumn = $this->option('duration-column');
        $this->operationsTable = $this->option('operations-table');
        $this->contractsTable = $this->option('contracts-table');
        $this->employeesTable = $this->option('employees-table');
        $this->worktimeFundTable = $this->option('worktime-table');
        $this->tasksTable = $this->option('tasks-table');
        $this->activityRecordsTable = $this->option('activity-records-table');
        $this->departmentsTable = $this->option('departments-table');
        $this->contextName = $this->option('context-name');
        $this->contextDescription = $this->option('context-description');

        try {
            if (!Schema::hasTable($this->sourceTable)) {
                $this->error("Source table '{$this->sourceTable}' does not exist");
                return 1;
            }

            $this->info("Processing records from table: {$this->sourceTable}");
            $this->processRecords();
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function processRecords(): void
    {
        $contextId = $this->batchService->getOrCreateBatchContext(
            $this->contextName,
            $this->contextDescription
        );
        $batchId = $this->batchService->createBatch($contextId);

        $records = DB::table($this->sourceTable)->get();

        if ($records->isEmpty()) {
            $this->warn('No records found in source table');
            return;
        }

        $this->info('Processing ' . $records->count() . ' records...');

        $contractIds = $records->pluck('employee_contract_id')->unique();
        $operationIds = $records->pluck('operation_id')->unique();

        // Fetch related data
        $contracts = DB::table($this->contractsTable)
            ->whereIn('id', $contractIds)
            ->get()
            ->keyBy('id');

        $employeeIds = $contracts->pluck('datahub_employee_id')->unique();
        $employees = DB::table($this->employeesTable)
            ->whereIn('id', $employeeIds)
            ->get()
            ->keyBy('id');

        $operations = DB::table($this->operationsTable)
            ->whereIn('id', $operationIds)
            ->get()
            ->keyBy('id');

        DB::transaction(function () use (
            $records,
            $contracts,
            $employees,
            $operations,
            $batchId
        ) {
            $processedCount = 0;
            $skippedCount = 0;

            foreach ($records as $record) {
                try {
                    $workDate = \Carbon\Carbon::createFromFormat(
                        $this->workDateFormat,
                        trim($record->{$this->workDateColumn})
                    )->format('Y-m-d');

                    $operationId = $record->operation_id;
                    $departmentId = $record->department_id;
                    $realDuration = (float) str_replace(',', '.', $record->{$this->activityRecordRealTimeColumn}) * 3600;

                    $employeeContract = $contracts->get($record->employee_contract_id);
                    if (!$employeeContract) {
                        $this->warn("Employee contract {$record->employee_contract_id} not found for record ID {$record->id}");
                        $skippedCount++;
                        continue;
                    }

                    $employee = $employees->get($employeeContract->datahub_employee_id);
                    if (!$employee) {
                        $this->warn("Employee not found for contract {$record->employee_contract_id}");
                        $skippedCount++;
                        continue;
                    }

                    $operation = $operations->get($operationId);
                    if (!$operation) {
                        $this->warn("Operation not found for id: {$operationId}");
                        $skippedCount++;
                        continue;
                    }

                    // Get or create worktime
                    $worktimeId = DB::table($this->worktimeFundTable)
                        ->where('personal_id', $employeeContract->pid)
                        ->where('date', $workDate)
                        ->where('department', $departmentId)
                        ->value('id');

                    if (!$worktimeId) {
                        $departmentCode = DB::table($this->departmentsTable)
                            ->where('id', $departmentId)
                            ->value('code');
                        $this->warn("Creating worktime for employee: {$employee->last_name} {$employee->first_name} on date: {$workDate} for department: {$departmentCode}");

                        $worktimeId = DB::table($this->worktimeFundTable)->insertGetId([
                            'date' => $workDate,
                            'first_name' => $employee->first_name,
                            'last_name' => $employee->last_name,
                            'personal_id' => $employeeContract->pid,
                            'shift' => 1,
                            'shift_start' => '00:00:00',
                            'shift_duration' => 0,
                            'department' => $departmentId,
                            'created_at' => $workDate,
                            'updated_at' => $workDate,
                        ]);

                        $this->batchService->logBatchRecord(
                            $batchId,
                            $worktimeId,
                            $this->worktimeFundTable
                        );
                    }

                    // Create task
                    $workTaskId = DB::table($this->tasksTable)->insertGetId([
                        'source_id' => $operationId,
                        'title' => $operation->title,
                        'expected_duration' => $operation->duration,
                        'is_shareable' => $operation->is_shareable,
                        'status' => 'started',
                        'department_id' => $departmentId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->batchService->logBatchRecord($batchId, $workTaskId, $this->tasksTable);

                    // Create activity record
                    $activityId = DB::table($this->activityRecordsTable)->insertGetId([
                        'title' => $operation->title,
                        'type' => 'O',
                        'expected_duration' => $operation->duration,
                        'real_duration' => $realDuration,
                        'is_official' => 1,
                        'is_fulfilled' => 1,
                        'date' => $workDate,
                        'start' => $workDate,
                        'end' => $workDate,
                        'personal_id' => $employeeContract->pid,
                        'department_id' => $departmentId,
                        'source_id' => 0,
                        'parent_id' => $worktimeId,
                        'task_id' => $workTaskId,
                    ]);
                    $this->batchService->logBatchRecord(
                        $batchId,
                        $activityId,
                        $this->activityRecordsTable
                    );

                    $processedCount++;
                } catch (\Exception $e) {
                    $this->error("Error processing record {$record->id}: " . $e->getMessage());
                    $skippedCount++;
                }
            }

            $this->info("Processed: {$processedCount} records");
            $this->info("Skipped: {$skippedCount} records");
        });
    }
}
