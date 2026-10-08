<?php

namespace Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\LookupScope;

use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleType;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;
use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchLookupScopeProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

/**
 * Profile providing scopes for form field data pickers
 */
class GenericProfile implements TaskBatchLookupScopeProfile
{
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
        return TaskGroup::query()
            ->whereIn('id', $context->departmentContext->taskGroupIds)
            ->get();
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
        return TaskItemGroup::query()
            ->whereIn('id', $context->departmentContext->taskItemGroupIds)
            ->get();
    }

    public function batchableTaskItemGroups(
        TaskBatchResourceContext $context,
    ): SupportCollection {
        return DB::table('tms_task_batch_department_tigs')            
            ->where('department_id', $context->departmentContext->departmentId)
            ->get();

        $data = [
            [
                'id' => 1,
                'label' => 'Grafity A',
                'short_label' => 'GA',
                'is_scalable' => 1,
            ],
            [
                'id' => 2,
                'label' => 'Grafity B',
                'short_label' => 'GB',
                'is_scalable' => 1,
            ],
            [
                'id' => 3,
                'label' => 'Cistenie B',
                'short_label' => 'Cistenie B',
                'is_scalable' => 0,
            ],
        ];
        return $data;
    }

    public function vehicleTypes(
        TaskBatchResourceContext $context,
    ): Collection {
        return VehicleType::query()
            ->whereIn('id', $context->departmentContext->vehicleTypeIds)
            ->get();
    }
}
