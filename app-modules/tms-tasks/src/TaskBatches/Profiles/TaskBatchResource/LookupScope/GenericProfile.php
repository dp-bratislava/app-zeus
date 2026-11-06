<?php

namespace Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\LookupScope;

use Carbon\CarbonImmutable;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleType;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;
use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchLookupScopeProfile;
use Dpb\Modules\Tasks\TaskBatches\Models\DepartmentTaskGroupView;
use Dpb\Modules\Tasks\TaskBatches\Models\DepartmentTaskItemGroupView;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Profile providing scopes for form field data pickers
 */
class GenericProfile implements TaskBatchLookupScopeProfile
{
    public function employeeContracts(
        TaskBatchResourceContext $context,
        CarbonImmutable $date
    ): array {
        return DB::table('mvw_hr_contract_snapshots as cs')
            ->join(
                'dpb_employeemanager_mm_group_datahubcontract as emgc',
                'emgc.datahub_contract_pid',
                '=',
                'cs.pid'
            )
            ->join(
                'dpb_employeemanager_model_group as emg',
                'emg.id',
                '=',
                'emgc.group_id'
            )
            ->join(
                'dpb_worktimefund_model_worktime as wt',
                'wt.personal_id',
                '=',
                'cs.pid'
            )
            ->where('emg.department', '=', $context->departmentContext->departmentId)
            ->where('wt.date', '=', $date->format('Y-m-d'))
            ->get()
            ->mapWithKeys(function ($contract) {
                return [
                    $contract->contract_id =>  $contract->pid . ' - ' . $contract->last_name
                ];
            })
            ->toArray();
    }

    public function taskSubjects(TaskBatchResourceContext $context): array
    {
        return Vehicle::with(['codes', 'licencePlates', 'model'])
            ->byTypeIds($context->departmentContext->vehicleTypeIds)
            ->get()
            ->mapWithKeys(function ($vehicle) {
                return [
                    $vehicle->id =>  $vehicle->label . ' - ' . $vehicle->model?->title
                ];
            })
            ->toArray();
    }

    public function taskGroups(TaskBatchResourceContext $context): Collection
    {
        return DepartmentTaskGroupView::query()
            ->where('department_id', $context->departmentContext->departmentId)
            ->whereIn('task_group_id', $context->departmentContext->taskGroupIds)
            ->get([
                'task_group_id as id',
                'task_group_label as label',
            ]);
    }

    public function maintenanceGroups(TaskBatchResourceContext $context): array
    {
        return MaintenanceGroup::whereIn('id', $context->departmentContext->maintenanceGroupIds)
            ->pluck('code', 'id')
            ->toArray();
    }

    public function taskItemGroups(
        TaskBatchResourceContext $context,
    ): Collection {
        return DepartmentTaskItemGroupView::query()
            ->where('department_id', $context->departmentContext->departmentId)
            ->whereIn('task_group_id', $context->departmentContext->taskGroupIds)
            ->get([
                'task_group_id',
                'task_item_group_id as id',
                'task_item_group_label as label',
                'is_scalable'
            ]); 
    }

    public function vehicleTypes(
        TaskBatchResourceContext $context,
    ): Collection {
        return VehicleType::query()
            ->whereIn('id', $context->departmentContext->vehicleTypeIds)
            ->get();
    }
}
