<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;

class VehicleSelect
{
    public static function make(
        string $name
    ): Component {
        return Select::make($name)
            ->label(__('dpb-mod-tasks::daily-maintenance.form.fields.vehicles'))
            ->searchable()
            ->multiple()
            ->required()
            ->validationMessages([
                'required' => __('dpb-mod-tasks::daily-maintenance.form.validations.vehicles_required'),
            ])
            //         ->reactive()
            // ->afterStateUpdated(function ($set, $get) {
            //     $set('selected_count', count($get('vehicles') ?? []));
            // })    
            ->live()
            ->dehydrated(false)
            ->options(function (
                TaskBatchLookupScopeService $lookupService,
            ) {
                return $lookupService
                    ->taskSubjects()
                    ->mapWithKeys(fn($vehicle) => [
                        $vehicle->vehicle_id => ($vehicle->code
                            ?? $vehicle->licence_plate
                            ?? 'N/A') . ' - ' . $vehicle->model
                    ])
                    ->toArray();
            });
    }
}
