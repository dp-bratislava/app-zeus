<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Forms;

use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;

class VehicleGroupForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema(static::schema())
            ->columns(7);
    }


    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('code')
                ->label(__('fleet/vehicle-group.form.fields.code.label'))
                ->columnSpan(1),
            Forms\Components\TextInput::make('title')
                ->label(__('fleet/vehicle-group.form.fields.title'))
                ->columnSpan(2),
            Forms\Components\TextInput::make('description')
                ->label(__('fleet/vehicle-group.form.fields.description'))
                ->columnSpan(4),
            // vehicels
            Forms\Components\CheckboxList::make('vehicles')
                ->label(__('fleet/vehicle-group.form.fields.vehicles'))
                ->options(function (Get $get) {
                    $type = $get('vehicle_type_id');
                    return Vehicle::get()
                        ->mapWithKeys(fn($vehicle) => [
                            $vehicle->id => $vehicle->code->code
                        ]);
                })
                ->searchable()
                ->bulkToggleable(true)
                ->columnSpanFull()
                ->columns(10)
        ];
    }
}
