<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;

class VehicleTypeFilter
{
    public static function make(
        string $name
    ): Component {
        // Note: You may want to change the field name if it's saving to a different column
        return ToggleButtons::make($name)
            ->label(__('dpb-mod-tasks::task-batch.form.filters.subject.vehicle_type'))
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
            ->dehydrated()
            ->live()
            ->inline();
    }
}
