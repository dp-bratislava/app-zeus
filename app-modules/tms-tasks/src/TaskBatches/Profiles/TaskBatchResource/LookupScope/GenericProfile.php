<?php

namespace Dpb\Modules\Tasks\Profiles\TaskBatchResource\LookupScope;

use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleType;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Dpb\Modules\Tasks\Context\TaskBatchResourceContext;
use Dpb\Modules\Tasks\Contracts\TaskBatchLookupScopeProfile;
use Illuminate\Database\Eloquent\Collection;

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

    public function vehicleTypes(
        TaskBatchResourceContext $context,
    ): Collection {
        return VehicleType::query()
            ->whereIn('id', $context->departmentContext->vehicleTypeIds)
            ->get();
    }
}
