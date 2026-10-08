<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\DB;

class VehicleSelect
{

    public static function make(?string $name = TaskBatchForm::COL_NAME_VEHICLE_PICKER): Component
    {
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
                // return DB::table('mvw_fleet_vehicle_snapshots')
                //     ->get([
                //         'vehicle_id',
                //         'code',
                //         'licence_plate',
                //     ])
                //     ->mapWithKeys(fn($vehicle) => [
                //         $vehicle->vehicle_id => ($vehicle->code ?? $vehicle->licence_plate) ?? 'g'
                //     ])->toArray();
                return $lookupService->taskSubjects();
            })
            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                $selectedVehicleIds = collect($state ?? [])
                    ->map(fn($id) => (int) $id);

                $currentTasks = collect(
                    $get(TaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG) ?? []
                );

                $vehicles = DB::table('mvw_fleet_vehicle_snapshots')
                    ->whereIn('vehicle_id', $selectedVehicleIds)
                    ->get([
                        'vehicle_id',
                        'code',
                        'licence_plate',
                        'model',
                        'length',
                        'seats',

                    ]);

                $tasks = $vehicles->map(function ($vehicle) use ($currentTasks) {

                    // Find existing row for this vehicle
                    $existing = $currentTasks->first(
                        fn($task) => (int) ($task['vehicle_id'] ?? 0) === (int) $vehicle->vehicle_id
                    );

                    // If it already exists, preserve EVERYTHING
                    if ($existing) {
                        return [
                            ...$existing,

                            // Update display information in case it changed
                            'vehicle_id' => $vehicle->vehicle_id,
                            'vehicle_label' => $vehicle->code
                                ?? $vehicle->licence_plate
                                ?? 'g',
                            'model' => $vehicle->model,
                            'length' => $vehicle->length,
                            'seats' => $vehicle->seats,
                        ];
                    }

                    // New vehicle → initialize defaults
                    return [
                        'vehicle_id' => $vehicle->vehicle_id,
                        'vehicle_label' => $vehicle->code
                            ?? $vehicle->licence_plate
                            ?? 'g',

                        'model' => $vehicle->model,
                        'length' => $vehicle->length,
                        'seats' => $vehicle->seats,
                        'cleaning_b' => false,
                        // 'ceiling' => false,
                        // 'tsv_mt' => false,
                        // 'tep_sv_st' => false,
                        // 'tep_s' => false,
                        // 'dezinf' => false,
                        // 'schody' => false,
                        // 'ga' => false,
                        // 'gb' => false,
                        // 'gc' => false,
                        // 'mp' => false,
                        // 'zz' => false,
                        // 'bz' => false,

                        'note' => null,
                    ];
                })->values()->all();

                $set(
                    TaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG,
                    $tasks
                );
            });
    }
}
