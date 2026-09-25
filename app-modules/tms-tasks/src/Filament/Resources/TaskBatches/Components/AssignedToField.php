<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Departments\Services\DepartmentService;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\VehicleType;
use Dpb\Package\TaskMS\Repositories\DepartmentAssignmentRepository;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class AssignedToField
{
    public static function make(?string $name = TaskBatchForm::COL_NAME_ASSIGNED_TO): Component
    {
        return ToggleButtons::make($name)
            ->label(__('dpb-mod-tasks::daily-maintenance.form.fields.assigned_to.label'))
            ->hidden(function (Get $get) {
                $typeId = $get(TaskBatchForm::COL_NAME_VEHICLE_TYPE_PICKER);
                if ($typeId) {
                    $vehicleType = VehicleType::find($typeId);
                    return $vehicleType && in_array($vehicleType->title, ['Električka', 'Trolejbus']);
                }
                return false;
            })
            ->options(function (DepartmentAssignmentRepository $daRepo, DepartmentService $departmentService) {
                $activeDeptId = $departmentService->getActiveDepartment()?->id;

                if ($activeDeptId) {
                    $mgIds = $daRepo->getMaintenanceGroupIdsByDepartmentId($activeDeptId);

                    return MaintenanceGroup::whereIn('id', $mgIds)
                        ->pluck('code', 'id')
                        ->toArray();
                }

                return [];
            })->afterStateUpdated(function (Set $set) {
                $set(TaskBatchForm::COL_NAME_VEHICLE_TYPE_PICKER, null);
            })
            ->dehydrated()
            ->live()
            ->inline();    
    }
}
