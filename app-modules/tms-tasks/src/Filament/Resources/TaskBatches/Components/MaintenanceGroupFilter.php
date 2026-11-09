<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Departments\Services\DepartmentService;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\TaskMS\Repositories\DepartmentAssignmentRepository;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;

class MaintenanceGroupFilter
{
    public static function make(
        string $name
    ): Component {
        return ToggleButtons::make($name)
            ->label(__('dpb-mod-tasks::task-batch.form.filters.subject.maintenance_group'))
            ->options(function (DepartmentAssignmentRepository $daRepo, DepartmentService $departmentService) {
                $activeDeptId = $departmentService->getActiveDepartment()?->id;

                if ($activeDeptId) {
                    $mgIds = $daRepo->getMaintenanceGroupIdsByDepartmentId($activeDeptId);

                    return MaintenanceGroup::whereIn('id', $mgIds)
                        ->pluck('code', 'id')
                        ->toArray();
                }

                return [];
            })
            ->dehydrated()
            ->live()
            ->inline();
    }
}
