<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Forms;

use App\Filament\Components\DepartmentPicker;
use Dpb\Package\Fleet\Models\DispatchGroup;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
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
                // code
                Forms\Components\TextInput::make('code')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.code.label'))
                    ->formatStateUsing(fn($record) => $record?->code?->code),
                // licence plate
                Forms\Components\TextInput::make('licence_plate')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.licence_plate'))
                    ->formatStateUsing(fn($record) => $record?->licencePlate),
                // vin
                Forms\Components\TextInput::make('vin')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.vin')),
                // model
                Forms\Components\Select::make('model_id')
                    ->columnSpan(3)
                    ->label(__('fleet/vehicle.form.fields.model'))
                    ->relationship('model', 'title')
                    ->preload()
                    ->live()
                    ->searchable(),
                // construction year
                Forms\Components\TextInput::make('construction_year')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.construction_year')),
                // warranty time start
                Forms\Components\DatePicker::make('warranty_initial_date')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.warranty_initial_date.label'))
                    ->hint(__('fleet/vehicle.form.fields.warranty_initial_date.hint')),
                // warranty time
                Forms\Components\TextInput::make('warranty_months')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.warranty_months.label'))
                    ->hint(__('fleet/vehicle.form.fields.warranty_months.hint')),
                // warranty distance km
                Forms\Components\TextInput::make('warranty_initial_km')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.warranty_initial_km.label'))
                    ->hint(__('fleet/vehicle.form.fields.warranty_initial_km.hint')),
                // warranty distance
                Forms\Components\TextInput::make('warranty_km')
                    ->columnSpan(1)
                    ->label(__('fleet/vehicle.form.fields.warranty_km.label'))
                    ->hint(__('fleet/vehicle.form.fields.warranty_km.hint')),

                Forms\Components\Section::make('zaradenie')
                    ->columns(3)
                    ->schema([
                        // maintenance group
                        Forms\Components\ToggleButtons::make('maintenance_group_id')
                            ->columnSpan(1)
                            ->label(__('fleet/vehicle.form.fields.maintenance_group'))
                            ->inline()
                            ->options(
                                fn(Vehicle $record) =>
                                MaintenanceGroup::byVehicleType($record->model?->type?->code)
                                    ->pluck('code', 'id')
                            ),
                        // department
                        DepartmentPicker::make('department')
                            ->columnSpan(1)
                            ->label(__('fleet/vehicle.form.fields.department.label'))
                            ->hint(__('fleet/vehicle.form.fields.department.hint'))
                            ->getOptionLabelFromRecordUsing(null)
                            ->getSearchResultsUsing(null)
                            ->searchable(),
                        // ->default(function)
                        // vehicle greoups
                        Forms\Components\Select::make('groups')
                            ->columnSpan(1)
                            ->label(__('fleet/vehicle.form.fields.groups'))
                            ->relationship('groups', 'title')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        // // dispatch group
                        //                 Forms\Components\ToggleButtons::make('dispatch_group')
                        //                     ->columnSpan(2)
                        //                     ->label(__('fleet/vehicle.form.fields.dispatch_group'))
                        //                     ->inline()
                        //                     ->options(fn() => DispatchGroup::pluck('code')),
                    ]),
            ])
        ;
    }
}
