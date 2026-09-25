<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\DB;

class VehiclePicker
{

    public static function make1(?string $name = TaskBatchForm::COL_NAME_VEHICLE_PICKER): Component
    {
        return CheckboxList::make($name)
            ->label(__('dpb-mod-tasks::daily-maintenance.form.fields.vehicles'))
            ->searchable()
            ->required()
            ->columns(10)
            ->validationMessages([
                'required' => __('dpb-mod-tasks::daily-maintenance.form.validations.vehicles_required'),
            ])
            ->searchPrompt('Hľadaj')
            ->bulkToggleable()
            //         ->reactive()
            // ->afterStateUpdated(function ($set, $get) {
            //     $set('selected_count', count($get('vehicles') ?? []));
            // })                    
            ->options(function (Get $get) {
                $mgId = $get(TaskBatchForm::COL_NAME_ASSIGNED_TO);
                $typeId = $get(TaskBatchForm::COL_NAME_VEHICLE_TYPE_PICKER);

                // 1. Initialize query
                // $query = Vehicle::with(['codes', 'model']);
                $query = Vehicle::query()
                    ->with([
                        'codes',
                        'licencePlates',
                    ])
                    ->leftJoin('fleet_vehicle_code_history as vch', function ($join) {
                        $join->on('fleet_vehicles.id', '=', 'vch.vehicle_id')
                            ->whereNull('vch.date_to'); // only current code
                    })
                    ->leftJoin('fleet_vehicle_codes as vc', 'vch.vehicle_code_id', '=', 'vc.id')
                    ->orderBy('fleet_vehicles.is_historic', 'asc')  // 0 first, 1 second
                    ->orderBy('vc.code', 'asc')

                    // ->limit(10)

                    ->select('fleet_vehicles.*'); // keep only vehicle columns

                // 2. Apply filters conditionally
                if ($mgId) {
                    $query->whereNotNull('maintenance_group_id')
                        ->byMaintenanceGroupId($mgId);
                } elseif ($typeId) {
                    // Using your model scope for IDs
                    $query->byTypeIds([$typeId]);
                } else {
                    // If nothing is selected, return empty to keep UI clean
                    return [];
                }

                // 3. Return formatted options
                return $query->get()
                    ->mapWithKeys(fn($vehicle) => [
                        $vehicle->id => $vehicle->label ?? ''
                    ])->toArray();
            });
    }

    public static function make(?string $name = TaskBatchForm::COL_NAME_VEHICLE_PICKER): Component
    {
        return CheckboxList::make($name)
            ->label(__('dpb-mod-tasks::daily-maintenance.form.fields.vehicles'))
            ->searchable()
            ->required()
            ->columns(12)
            ->validationMessages([
                'required' => __('dpb-mod-tasks::daily-maintenance.form.validations.vehicles_required'),
            ])
            ->searchPrompt('Hľadaj')
            ->bulkToggleable()
            //         ->reactive()
            // ->afterStateUpdated(function ($set, $get) {
            //     $set('selected_count', count($get('vehicles') ?? []));
            // })                    
            ->options(function (Get $get) {
                $mgId = $get(TaskBatchForm::COL_NAME_ASSIGNED_TO);
                $typeId = $get(TaskBatchForm::COL_NAME_VEHICLE_TYPE_PICKER);

                // 1. Initialize query
                // $query = Vehicle::with(['codes', 'model']);
                $query = DB::table('mvw_fleet_vehicle_snapshots');
                // 2. Apply filters conditionally
                if ($mgId) {
                    $query->whereNotNull('maintenance_group_id')
                        ->where('maintenance_group_id', '=', $mgId);
                } elseif ($typeId) {
                    // Using your model scope for IDs
                    $query->where('type_id', '=', $typeId);
                } else {
                    // If nothing is selected, return empty to keep UI clean
                    return [];
                }

                // 3. Return formatted options
                return $query
                    ->get([
                        'vehicle_id',
                        'code',
                        'licence_plate',
                    ])
                    ->mapWithKeys(fn($vehicle) => [
                        $vehicle->vehicle_id => ($vehicle->code ?? $vehicle->licence_plate) ?? 'g'
                    ])->toArray();
            });
    }
}
