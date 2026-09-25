<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Set;

class VehicleTypePicker
{
    public static function make(?string $name = TaskBatchForm::COL_NAME_VEHICLE_TYPE_PICKER): Component
    {
        // Note: You may want to change the field name if it's saving to a different column
        return ToggleButtons::make($name)
            ->label(__('dpb-mod-tasks::task-batch.form.fields.vehicle_type_id.label'))
            ->options(
                fn(
                    TaskBatchLookupScopeService $lookupService
                ): array => $lookupService
                    ->vehicleTypes()
                    ->pluck('title', 'id')
                    ->toArray()
            )
            ->default(
                function (
                    TaskBatchLookupScopeService $lookupService
                ) {
                    $vehicleTypes = $lookupService->vehicleTypes();

                    if ($vehicleTypes->count() === 1) {
                        return [$vehicleTypes->first()->id];
                    }

                    return null;
                }
            )
            ->afterStateUpdated(function (Set $set) {
                $set(TaskBatchForm::COL_NAME_ASSIGNED_TO, null);
            })
            ->dehydrated()
            ->live()
            ->inline();
    }
}
