<?php

namespace App\Console\Commands;

use App\Console\Services\BatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

abstract class AsphereImportBase extends Command
{
    protected BatchService $batchService;
    protected string $tableName;
    protected string $creationDateColumn = 'Dátum a čas vzniku';
    protected string $workDateFormat = 'd.m.Y';
    protected string $creationDateFormat = 'd.m.Y H:i';
    protected string $workDateColumn = 'Dátum výkonu pracovníka';
    protected string $activityRecordRealTimeColumn = 'Čas [hod]';
    protected bool $usesInspection = false;
    protected bool $usesTitleDescription = false;

    protected function getMaintananceGroupFromVehicleId($vehicleId)
    {
        return DB::table('fleet_vehicles')
            ->where('id', $vehicleId)
            ->value('maintenance_group_id') ?? 1;
    }

    public function __construct(BatchService $batchService)
    {
        parent::__construct();
        $this->batchService = $batchService;
    }

    public function handle()
    {
        try {
            $this->info('Grouping records by vehicle and task item group...');
            $groupedRecords = $this->groupRecordsByTaskCreationDate($this->creationDateColumn);

            $this->info('Pocet zakaziek: ' . count($groupedRecords));

            $this->createTasksWithTaskItems($groupedRecords);
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    protected function createTasksWithTaskItems(array $groupedRecords): void
    {
        $this->info('creating tasks with task items.');

        $contextId = $this->batchService->getOrCreateBatchContext(
            'Asphere_TaskItem_Creation',
            'Pridanie zakazok a podzakazok import z aspheru'
        );
        $batchId = $this->batchService->createBatch($contextId);
        
        $taskGroupCode = $this->usesInspection ? 'inspection' : 'malfunction';
        $taskGroup = DB::table('tsk_task_groups')->get()
            ->where('code', $taskGroupCode)
            ->first();
        
        foreach ($groupedRecords as $groupData) {
            $records = collect($groupData['records']);
            $vehicleId = $groupData['vehicle_id'];
            $date = $groupData['creation_date'];
            $authorId = $groupData['author_id'];
            $taskItemGroupId = $groupData['task_item_group_id'];


            $taskData = [
                'date' => $date,
                'group_id' => $taskGroup->id,
                'place_of_origin_id' => 1,
                'state' => 'created',
                'created_at' => $date,
                'updated_at' => $date,
            ];

            if ($this->usesTitleDescription) {
                $taskData['description'] = $groupData['description'];
                $taskData['title'] = $groupData['title'];
            }

            $taskId = DB::table('tsk_tasks')->insertGetId($taskData);
            $this->batchService->logBatchRecord($batchId, $taskId, 'tsk_tasks');

            $maintenanceGroupId = $this->getMaintananceGroupFromVehicleId($vehicleId);

            $inspectionId = null;
            if ($this->usesInspection) {
                $inspectionId = $this->createInspectionForRecord(
                    $vehicleId,
                    $groupData['inspection_template_id'],
                    $date,
                    $batchId
                );
            }

            $taskAssignmentData = [
                'task_id' => $taskId,
                'subject_id' => $vehicleId,
                'subject_type' => 'vehicle',
                'author_id' => $authorId,
                'assigned_to_id' => $maintenanceGroupId,
                'assigned_to_type' => 'maintenance-group',
                'created_at' => $date,
                'updated_at' => $date,
            ];

            if ($inspectionId) {
                $taskAssignmentData['source_id'] = $inspectionId;
                $taskAssignmentData['source_type'] = 'inspection';
            }

            $taskAssignmentId = DB::table('tms_task_assignments')->insertGetId($taskAssignmentData);
            $this->batchService->logBatchRecord($batchId, $taskAssignmentId, 'tms_task_assignments');

            $taskItemId = DB::table('tsk_task_items')->insertGetId([
                'date' => $date,
                'task_id' => $taskId,
                'state' => 'created',
                'group_id' => $taskItemGroupId,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $this->batchService->logBatchRecord($batchId, $taskItemId, 'tsk_task_items');

            $taskItemAssignmentId = DB::table('tms_task_item_assignments')->insertGetId([
                'task_item_id' => $taskItemId,
                'author_id' => $authorId,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $this->batchService->logBatchRecord($batchId, $taskItemAssignmentId, 'tms_task_item_assignments');

            $workOrderId = DB::table('dpb_wtftmsbridge_model_workorder')->insertGetId([
                'tms_task_item_id' => $taskItemId,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $contractIds = $records->pluck('employee_contract_id')->unique();
            $operationIds = $records->pluck('operation_id')->unique();

            $contracts = DB::table('datahub_employee_contracts')
                ->whereIn('id', $contractIds)
                ->get()
                ->keyBy('id');

            $employeeIds = $contracts->pluck('datahub_employee_id')->unique();
            $employees = DB::table('datahub_employees')
                ->whereIn('id', $employeeIds)
                ->get()
                ->keyBy('id');

            $operations = DB::table('dpb_worktimefund_model_operation')
                ->whereIn('id', $operationIds)
                ->get()
                ->keyBy('id');

            DB::transaction(function () use (
                $records,
                $contracts,
                $employees,
                $operations,
                $batchId,
                $workOrderId,
                $date
            ) {
                foreach ($records as $record) {
                    $workDate = \Carbon\Carbon::createFromFormat(
                        $this->workDateFormat,
                        trim($record->{$this->workDateColumn})
                    )->format('Y-m-d');

                    $operationId = $record->operation_id;
                    $departmentId = $record->department_id;
                    $realDuration = (float) str_replace(',', '.', $record->{$this->activityRecordRealTimeColumn}) * 3600;

                    $employeeContract = $contracts->get($record->employee_contract_id);
                    $employee = $employees->get($employeeContract->datahub_employee_id);
                    $operation = $operations->get($operationId);

                    $worktimeId = DB::table('dpb_worktimefund_model_worktime')
                        ->where('personal_id', $employeeContract->pid)
                        ->where('date', $workDate)
                        ->where('department', $departmentId)
                        ->value('id');

                    if (!$worktimeId) {
                        $departmentCode= DB::table('datahub_departments')
                            ->where('id', $departmentId)
                            ->value('code');
                        $this->error("NO worktime for employee: {$employee->last_name} {$employee->first_name} on date: {$workDate} for department: {$departmentCode}");

                        $worktimeId = DB::table('dpb_worktimefund_model_worktime')->insertGetId([
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
                            'dpb_worktimefund_model_worktime'
                        );
                    }

                    if (!$operation) {
                        $this->warn("Operation not found for id: {$operationId}");
                        continue;
                    }

                    $workTaskId = DB::table('dpb_worktimefund_model_task')->insertGetId([
                        'source_id' => $operationId,
                        'title' => $operation->title,
                        'expected_duration' => $operation->duration,
                        'is_shareable' => $operation->is_shareable,
                        'status' => 'started',
                        'department_id' => $departmentId,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                    $this->batchService->logBatchRecord($batchId, $workTaskId, 'dpb_worktimefund_model_task');

                    DB::table('dpb_wtftmsbridge_mm_workorder_task')->insert([
                        'workorder_id' => $workOrderId,
                        'taskitem_id' => $workTaskId,
                    ]);

                    $activityId = DB::table('dpb_worktimefund_model_activityrecord')->insertGetId([
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
                        'dpb_worktimefund_model_activityrecord'
                    );
                }
            });
        }
    }

    protected function createInspectionForRecord(
        int $vehicleId,
        int $inspectionTemplateId,
        $creationDate,
        int $batchId
    ): int {
        $date = is_string($creationDate) ? \Carbon\Carbon::parse($creationDate) : $creationDate;

        $inspectionId = DB::table('insp_inspections')->insertGetId([
            'date' => $date->format('Y-m-d'),
            'template_id' => $inspectionTemplateId,
            'state' => 'upcoming',
            'created_at' => $date->format('Y-m-d H:i:s'),
            'updated_at' => $date->format('Y-m-d H:i:s'),
        ]);

        $this->batchService->logBatchRecord($batchId, $inspectionId, 'insp_inspections');

        $inspectionAssignmentId = DB::table('tms_inspection_assignments')->insertGetId([
            'inspection_id' => $inspectionId,
            'subject_id' => $vehicleId,
            'subject_type' => 'vehicle',
            'created_at' => $date->format('Y-m-d H:i:s'),
            'updated_at' => $date->format('Y-m-d H:i:s'),
        ]);

        $this->batchService->logBatchRecord(
            $batchId,
            $inspectionAssignmentId,
            'tms_inspection_assignments'
        );

        return $inspectionId;
    }

    protected function groupRecordsByTaskCreationDate(string $creationDateColumnName): array
    {
        $allRecords = DB::table($this->tableName)
            ->whereNotNull('vehicle_id')
            ->whereNotNull('task_item_group_id')
            ->get();

        $grouped = [];

        foreach ($allRecords as $record) {
            $dateValue = $record->$creationDateColumnName;

            if (is_string($dateValue)) {
                $dateFormatted = \Carbon\Carbon::createFromFormat(
                    $this->creationDateFormat,
                    $dateValue
                )->format('Y-m-d H:i');
            } else {
                $dateFormatted = $dateValue->format('Y-m-d H:i');
            }

            $groupKey = "{$record->vehicle_id}_{$record->task_item_group_id}_{$dateFormatted}";

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'creation_date' => $dateFormatted,
                    'vehicle_id' => $record->vehicle_id,
                    'task_item_group_id' => $record->task_item_group_id,
                    'author_id' => $record->author_id,
                    'records' => [],
                ];

                if ($this->usesInspection) {
                    $grouped[$groupKey]['inspection_template_id'] = $record->inspection_template_id;
                }

                if ($this->usesTitleDescription) {
                    $grouped[$groupKey]['description'] = $record->Poznámka;
                    $grouped[$groupKey]['title'] = $record->{'Detail poruchy'};
                }
            }

            $grouped[$groupKey]['records'][] = $record;
        }

        return $grouped;
    }
}
