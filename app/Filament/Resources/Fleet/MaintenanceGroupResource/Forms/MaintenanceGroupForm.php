<?php

namespace App\Filament\Resources\Fleet\Vehicle\MaintenanceGroupResource\Forms;

use Dpb\Package\Fleet\Models\VehicleType;
use Filament\Forms;
use Filament\Forms\Form;

class MaintenanceGroupForm
{
    public static function make(Form $form): Form
    {
        return $form->schema(static::schema());
    }


    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('code')
                ->columnSpan(1)
                ->label(__('fleet/maintenance-group.form.fields.code.label')),
            Forms\Components\TextInput::make('title')
                ->columnSpan(1)
                ->label(__('fleet/maintenance-group.form.fields.title')),
            Forms\Components\TextInput::make('description')
                ->columnSpan(1)
                ->label(__('fleet/maintenance-group.form.fields.description')),
            Forms\Components\ColorPicker::make('color')
                ->columnSpan(1)
                ->label(__('fleet/maintenance-group.form.fields.color')),
            Forms\Components\ToggleButtons::make('vehicle_type_id')
                ->label(__('fleet/maintenance-group.form.fields.vehicle_type'))
                ->options(fn() => VehicleType::pluck('title', 'id'))
                ->inline(),
        ];
    }
}
