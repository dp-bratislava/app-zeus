<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Forms;

use App\Filament\Resources\Fleet\Vehicle\BrandResource\Froms\BrandPicker;
use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Forms\VehicleTypePicker;
use Filament\Forms;
use Filament\Forms\Form;

class VehicleModelForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([
                BrandPicker::make('brand_id', $form)
                    ->relationship('brand', 'title'),

                // title
                Forms\Components\TextInput::make('title')
                    ->label(__('fleet/vehicle-model.form.fields.title.label')),
                //year
                Forms\Components\TextInput::make('year')
                    ->label(__('fleet/vehicle-model.form.fields.year.label'))
                    ->numeric(),
                // type
                VehicleTypePicker::make('type_id', $form)
                    ->relationship('type', 'title'),

            ]);
    }
}
