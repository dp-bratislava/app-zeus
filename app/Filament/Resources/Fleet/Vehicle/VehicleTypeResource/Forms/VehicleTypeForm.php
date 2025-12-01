<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Forms;

use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Forms;
use Filament\Forms\Form;

class VehicleTypeForm
{
    public static function make(Form $form): Form
    {
        return $form->schema(static::schema());
    }

    public static function schema(): array
    {
        return [
            // code
            Forms\Components\TextInput::make('code')
                ->label(__('fleet/vehicle-type.form.fields.code.label')),
            // title
            Forms\Components\TextInput::make('title')
                ->label(__('fleet/vehicle-type.form.fields.title')),
            // models
            Forms\Components\CheckboxList::make('models')
                ->label(__('fleet/vehicle-type.form.fields.models'))
                ->options(function () {
                    return VehicleModel::get()
                        ->mapWithKeys(fn($vehicleModel) => [
                            $vehicleModel->id => $vehicleModel->title
                        ]);
                })
                ->searchable()
                ->bulkToggleable(true)
                ->columnSpanFull()
                ->columns(5)

        ];
    }
}
