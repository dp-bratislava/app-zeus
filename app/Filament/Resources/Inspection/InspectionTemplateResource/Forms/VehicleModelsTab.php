<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Forms;

use App\Filament\Resources\Fleet\Vehicle\BrandResource\Forms\BrandPicker;
use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Forms\VehicleTypePicker;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Forms;
use Filament\Forms\Form;

class VehicleModelsTab
{
    public static function make(): array
    {
        return [
            Forms\Components\CheckboxList::make('vehicle_models')
                ->label(__('inspections/inspection-template.form.fields.vehicle_models'))
                ->options(function () {
                    return VehicleModel::get()
                        ->mapWithKeys(fn($vehicle) => [
                            $vehicle->id => $vehicle->title
                        ]);
                })
                ->searchable()
                ->bulkToggleable(true)
                ->columnSpanFull()
                ->columns(10)
        ];
    }
}
