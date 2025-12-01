<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Imports\Fleet\VehicleTypeImporter;
use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Forms\VehicleTypeForm;
use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Pages;
use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Tables\VehicleTypeTable;
use Dpb\Package\Fleet\Models\VehicleType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    public static function getModelLabel(): string
    {
        return __('fleet/vehicle-type.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/vehicle-type.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/vehicle-type.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/vehicle-type.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-fleet.navigation.vehicle-type') ?? 999;
    }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->can('fleet.vehicle-type.read');
    // }

    public static function form(Form $form): Form
    {
        return VehicleTypeForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return VehicleTypeTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleTypes::route('/'),
            'create' => Pages\CreateVehicleType::route('/create'),
            'edit' => Pages\EditVehicleType::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->can('fleet.vehicle-type.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->check() && auth()->user()->can('fleet.vehicle-type.update');
    }   

    public static function canDelete(Model $record): bool
    {        
        return auth()->check() && auth()->user()->can('fleet.vehicle-type.delete');
    }    
}
