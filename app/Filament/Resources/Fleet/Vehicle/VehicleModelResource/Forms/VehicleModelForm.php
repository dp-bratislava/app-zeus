<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Forms;

use Filament\Forms;
use Filament\Forms\Form;

class VehicleModelForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('fleet/vehicle-model.form.fields.title.label')),
                Forms\Components\TextInput::make('year')
                    ->label(__('fleet/vehicle-model.form.fields.year.label'))
                    ->numeric(),

                Forms\Components\Select::make('type_id')                
                    ->label(__('fleet/vehicle-model.form.fields.type.label'))
                    ->relationship('type', 'title')
                    ->searchable()
                    ->preload()
            ]);
    }
}
