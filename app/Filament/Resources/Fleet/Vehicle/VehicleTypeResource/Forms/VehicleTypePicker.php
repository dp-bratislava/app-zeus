<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Forms;

use Filament\Forms;

class VehicleTypePicker
{
    public static function make(string $uri)
    {
        return Forms\Components\Select::make($uri)
            ->label(__('fleet/vehicle-type.components.picker.label'))
            ->searchable()
            ->preload()
            ->createOptionForm(VehicleTypeForm::schema())
            ->createOptionModalHeading(__('fleet/vehicle-type.components.picker.create_heading'))
            ->editOptionForm(VehicleTypeForm::schema())
            ->editOptionModalHeading(__('fleet/vehicle-type.components.picker.update_heading'));
    }
}
