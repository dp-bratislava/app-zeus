<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\UniformTaskBatchSchema;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\DB;

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
            //         ->reactive()
            // ->afterStateUpdated(function ($set, $get) {
            //     $set('selected_count', count($get('vehicles') ?? []));
            // })                    
            ->options(function (Get $get) {
                $mgId = $get(UniformTaskBatchSchema::MAINTENANCE_GROUP_FILTER_KEY);
                $typeId = $get(UniformTaskBatchSchema::VEHICLE_TYPE_FILTER_KEY);

                // 1. Initialize query
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
