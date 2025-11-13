<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Imports\Fleet\VehicleGroupImporter;
use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Forms\VehicleGroupForm;
use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Pages;
use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Tables\VehicleGroupTable;
use Dpb\Package\Fleet\Models\VehicleGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehicleGroupResource extends Resource
{
    protected static ?string $model = VehicleGroup::class;

    public static function getModelLabel(): string
    {
        return __('fleet/vehicle-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/vehicle-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/vehicle-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/vehicle-group.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-fleet.navigation.vehicle-group') ?? 999;
    }

    public static function form(Form $form): Form
    {
        return VehicleGroupForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return VehicleGroupTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleGroups::route('/'),
            'create' => Pages\CreateVehicleGroup::route('/create'),
            'edit' => Pages\EditVehicleGroup::route('/{record}/edit'),
        ];
    }

    // public static function canCreate(): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-group.create');
    // }

    // public static function canEdit(Model $record): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-group.update');
    // }   
    
    // public static function canDelete(Model $record): bool
    // {        
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-group.delete');
    // }       
}
