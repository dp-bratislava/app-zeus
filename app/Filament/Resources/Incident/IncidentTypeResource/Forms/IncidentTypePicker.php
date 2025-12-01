<?php

namespace App\Filament\Resources\Incident\IncidentTypeResource\Forms;

use Dpb\Package\Incidents\Models\IncidentType;
use Filament\Forms;

class IncidentTypePicker
{
    public static function make(string $uri)
    {
        return Forms\Components\ToggleButtons::make($uri)
            ->label(__('incidents/incident.form.fields.type'))
            ->inline()
            ->columnSpan(1)
            ->options(
                fn() =>
                IncidentType::pluck('title', 'id')
            );
        // return Forms\Components\Select::make($uri)
        //     ->label(__('fleet/vehicle-type.components.picker.label'))
        //     ->searchable()
        //     ->preload()
        //     ->createOptionForm(VehicleTypeForm::schema())
        //     ->createOptionModalHeading(__('fleet/vehicle-type.components.picker.create_heading'))
        //     ->editOptionForm(VehicleTypeForm::schema())
        //     ->editOptionModalHeading(__('fleet/vehicle-type.components.picker.update_heading'));
    }
}
