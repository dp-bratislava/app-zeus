<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\UniformTaskBatchSchema;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class VehiclePicker
{
    public static function make(
        string $name
    ): Component {
        return CheckboxList::make($name)
            ->label(__('dpb-mod-tasks::daily-maintenance.form.fields.vehicles'))
            ->searchable()
            // ->required()
            ->columns(12)
            ->validationMessages([
                'required' => __('dpb-mod-tasks::daily-maintenance.form.validations.vehicles_required'),
            ])
            ->searchPrompt('Hľadaj')
            ->bulkToggleable()
            ->options(function (Get $get, TaskBatchLookupScopeService $lookupService) {
                $mgId = $get(UniformTaskBatchSchema::MAINTENANCE_GROUP_FILTER_KEY);
                $typeId = $get(UniformTaskBatchSchema::VEHICLE_TYPE_FILTER_KEY);

                return $lookupService->taskSubjects()
                    ->filter(function ($vehicle) use ($mgId, $typeId) {
                        if ($mgId) {
                            return $vehicle->maintenance_group_id == $mgId;
                        }

                        if ($typeId) {
                            return $vehicle->type_id == $typeId;
                        }

                        return true;
                    })
                    ->mapWithKeys(fn($vehicle) => [
                        $vehicle->vehicle_id => $vehicle->code
                            ?? $vehicle->licence_plate
                            ?? 'N/A',
                    ])
                    ->toArray();
            });
    }
}
