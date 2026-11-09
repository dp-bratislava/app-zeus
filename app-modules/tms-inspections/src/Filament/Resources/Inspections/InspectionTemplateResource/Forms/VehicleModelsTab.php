<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Forms;

use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleModel;
use Dpb\Package\Fleet\Models\VehicleType;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;

class VehicleModelsTab
{
    private const UNASSIGNED = '__unassigned__';

    public static function make(): array
    {
        return [
            // vehicle types
            Forms\Components\ToggleButtons::make('vehicle_type_id')
                ->label(__('dpb-mod-inspections::inspection-template.form.fields.vehicle_types'))
                ->options(
                    function () {
                        // return VehicleType::pluck('title', 'id');
                        return
                            VehicleType::pluck('title', 'id')
                                ->union(
                                    [self::UNASSIGNED => __('Nezaradené')],
                                );
                        // ->toArray();
                    }
                )
                ->dehydrated()
                ->live()
                ->inline(),

            // vehicle models
            Forms\Components\CheckboxList::make('templatables.vehicle-model')
                ->label(__('dpb-mod-inspections::inspection-template.form.fields.templatables.label'))
                ->hint(__('dpb-mod-inspections::inspection-template.form.fields.templatables.hint'))
                ->options(function (Get $get) {
                    $vehicleTypeId = $get('vehicle_type_id');
                    // return VehicleModel::byTypeIds([$vehicleTypeId])
                    //     ->get()
                    //     ->mapWithKeys(fn($vehicleModel) => [
                    //         $vehicleModel->id => $vehicleModel->title
                    //     ]);
                    if ($vehicleTypeId === self::UNASSIGNED) {
                        return VehicleModel::whereNull('type_id')
                            ->pluck('title', 'id');
                    }

                    if (! $vehicleTypeId) {
                        return [];
                    }

                    return VehicleModel::byTypeIds([$vehicleTypeId])
                        ->pluck('title', 'id');
                })
                ->searchable()
                ->bulkToggleable(true)
                ->columnSpanFull()
                ->columns(10),
        ];
    }
}
