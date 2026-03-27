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

    protected string $noteColumn = 'Poznámka';
    protected string $activityRecordRealTimeColumn = 'Čas [hod]';
    protected string $importType = 'malfunction'; // 'inspection', 'malfunction', or 'daily_inspection'

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

    protected function getTypeConfig(): array
    {
        $configs = [
            'inspection' => [
                'task_group_code' => 'inspection',
                'create_inspection' => true,
                'use_title_description' => true,
            ],
            'malfunction' => [
                'task_group_code' => 'malfunction',
                'create_inspection' => false,
                'use_title_description' => false,
            ],
            'daily-maintenance' => [
                'task_group_code' => 'daily-maintenance',
                'create_inspection' => false,
                'use_title_description' => false,
            ],
        ];

        return $configs[$this->importType] ?? $configs['malfunction'];
    }

    public function handle()
    {
        try {
            $this->info('Grouping records by vehicle and task item group...');
            $groupedRecords = $this->groupRecordsByTaskCreationDate($this->creationDateColumn,$this->getTypeConfig());

            $this->info('Pocet zakaziek: ' . count($groupedRecords));

            $this->createTasksWithTaskItems($groupedRecords);
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    protected function createTasksWithTaskItems(array $groupedRecords): void
    {
        $this->info('creating tasks with task items.');

        $config = $this->getTypeConfig();

        $contextId = $this->batchService->getOrCreateBatchContext(
            'Asphere_TaskItem_Creation',
            'Pridanie zakazok a podzakazok import z aspheru'
        );
        $batchId = $this->batchService->createBatch($contextId);
        
        // Collect all batch records for bulk insert at the end
        $batchRecords = [];
        
        $taskGroup = DB::table('tsk_task_groups')->get()
            ->where('code', $config['task_group_code'])
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

            if ($config['use_title_description']) {
                $taskData['description'] = $groupData['description'];
                $taskData['title'] = $groupData['title'];
            }

            $taskId = DB::table('tsk_tasks')->insertGetId($taskData);
            $batchRecords[] = [
                'batch_id' => $batchId,
                'record_id' => $taskId,
                'record_type' => 'tsk_tasks',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $maintenanceGroupId = $this->getMaintananceGroupFromVehicleId($vehicleId);

            $inspectionId = null;
            if ($config['create_inspection']) {
                $inspectionId = $this->createInspectionForRecord(
                    $vehicleId,
                    $groupData['inspection_template_id'],
                    $date,
                    $batchId,
                    $batchRecords
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
            $batchRecords[] = [
                'batch_id' => $batchId,
                'record_id' => $taskAssignmentId,
                'record_type' => 'tms_task_assignments',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $taskItemId = DB::table('tsk_task_items')->insertGetId([
                'date' => $date,
                'task_id' => $taskId,
                'state' => 'created',
                'group_id' => $taskItemGroupId,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $batchRecords[] = [
                'batch_id' => $batchId,
                'record_id' => $taskItemId,
                'record_type' => 'tsk_task_items',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $taskItemAssignmentId = DB::table('tms_task_item_assignments')->insertGetId([
                'task_item_id' => $taskItemId,
                'author_id' => $authorId,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $batchRecords[] = [
                'batch_id' => $batchId,
                'record_id' => $taskItemAssignmentId,
                'record_type' => 'tms_task_item_assignments',
                'created_at' => now(),
                'updated_at' => now(),
            ];

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
                $date,
                &$batchRecords
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

                        $batchRecords[] = [
                            'batch_id' => $batchId,
                            'record_id' => $worktimeId,
                            'record_type' => 'dpb_worktimefund_model_worktime',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
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
                        'maintainable_type' => 'Dpb\\WorkTimeFund\\Models\\Maintainables\\Vehicle',
                        'maintainable_id' => $record->vehicle_id,
                    ]);
                    $batchRecords[] = [
                        'batch_id' => $batchId,
                        'record_id' => $workTaskId,
                        'record_type' => 'dpb_worktimefund_model_task',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

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
                    $batchRecords[] = [
                        'batch_id' => $batchId,
                        'record_id' => $activityId,
                        'record_type' => 'dpb_worktimefund_model_activityrecord',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            });
        }
        
        // Bulk insert all collected batch records at the end
        if (!empty($batchRecords)) {
            $this->batchService->logBatchRecordMultiple($batchRecords);
        }
    }

    protected function createInspectionForRecord(
        int $vehicleId,
        int $inspectionTemplateId,
        $creationDate,
        int $batchId,
        array &$batchRecords
    ): int {
        $date = is_string($creationDate) ? \Carbon\Carbon::parse($creationDate) : $creationDate;

        $inspectionId = DB::table('insp_inspections')->insertGetId([
            'date' => $date->format('Y-m-d'),
            'template_id' => $inspectionTemplateId,
            'state' => 'upcoming',
            'created_at' => $date->format('Y-m-d H:i:s'),
            'updated_at' => $date->format('Y-m-d H:i:s'),
        ]);

        $batchRecords[] = [
            'batch_id' => $batchId,
            'record_id' => $inspectionId,
            'record_type' => 'insp_inspections',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $inspectionAssignmentId = DB::table('tms_inspection_assignments')->insertGetId([
            'inspection_id' => $inspectionId,
            'subject_id' => $vehicleId,
            'subject_type' => 'vehicle',
            'created_at' => $date->format('Y-m-d H:i:s'),
            'updated_at' => $date->format('Y-m-d H:i:s'),
        ]);

        $batchRecords[] = [
            'batch_id' => $batchId,
            'record_id' => $inspectionAssignmentId,
            'record_type' => 'tms_inspection_assignments',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return $inspectionId;
    }

    protected function groupRecordsByTaskCreationDate(string $creationDateColumnName,$config): array
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
            if($this->importType === 'daily-maintenance'){
                $groupKey .= "_{$record->id}";
            }
            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'creation_date' => $dateFormatted,
                    'vehicle_id' => $record->vehicle_id,
                    'task_item_group_id' => $record->task_item_group_id,
                    'author_id' => $record->author_id,
                    'records' => [],
                ];

                if ($config['create_inspection']) {
                    $grouped[$groupKey]['inspection_template_id'] = $record->inspection_template_id;
                }

                if ($config['use_title_description']) {
                    $grouped[$groupKey]['description'] = $record->{$this->noteColumn};
                    $grouped[$groupKey]['title'] = $record->{'Detail poruchy'};
                }
            }

            $grouped[$groupKey]['records'][] = $record;
        }

        return $grouped;
    }
}
