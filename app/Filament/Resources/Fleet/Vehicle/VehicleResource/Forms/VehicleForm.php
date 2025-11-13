<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Forms;

use App\Filament\Components\DepartmentPicker;
use Dpb\Package\Fleet\Models\DispatchGroup;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;

class VehicleForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([

                Forms\Components\TextInput::make('vin')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.vin')),
                // licence plate
                Forms\Components\TextInput::make('licence_plate')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.licence_plate'))
                    ->formatStateUsing(fn($record) => $record?->licencePlate),
                // code
                Forms\Components\TextInput::make('code')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.code.label'))
                    ->formatStateUsing(fn($record) => $record?->code?->code),
                // mdoel
                Forms\Components\Select::make('model_id')
                    ->columnSpan(3)
                    ->label(__('fleet/vehicle.form.fields.model'))
                    ->relationship('model', 'title')
                    ->preload()
                    ->searchable(),
                // department
                DepartmentPicker::make('department')
                    ->columnSpan(2)
                    ->label(__('fleet/vehicle.form.fields.department'))
                    ->getOptionLabelFromRecordUsing(null)
                    ->getSearchResultsUsing(null)
                    ->searchable(),

                Forms\Components\Select::make('groups')
                    ->columnSpan(2)
                    ->label(__('fleet/vehicle.form.fields.groups'))
                    ->relationship('groups', 'title')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                // ->default(function)

                Forms\Components\ToggleButtons::make('maintenance_group_id')
                    ->columnSpan(2)
                    ->label(__('fleet/vehicle.form.fields.maintenance_group'))
                    ->inline()
                    ->options(fn() => MaintenanceGroup::pluck('code', 'id')),

                Forms\Components\ToggleButtons::make('dispatch_group')
                    ->columnSpan(2)
                    ->label(__('fleet/vehicle.form.fields.dispatch_group'))
                    ->inline()
                    ->options(fn() => DispatchGroup::pluck('code')),
            ])
        ;
    }
}
