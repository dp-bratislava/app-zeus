<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Forms\VehicleModelForm;
use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Pages;
use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Tables\VehicleModelTable;
use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Infolists\VehicleModelInfolist;
use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VehicleModelResource extends Resource
{
    protected static ?string $model = VehicleModel::class;

    public static function getModelLabel(): string
    {
        return __('fleet/vehicle-model.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/vehicle-model.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/vehicle-model.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/vehicle-model.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-fleet.navigation.vehicle-model') ?? 999;
    }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->can('fleet.vehicle-model.read');
    // }

    public static function form(Form $form): Form
    {
        return VehicleModelForm::make($form);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return VehicleModelInfolist::make($infolist);
    }

    public static function table(Table $table): Table
    {
        return VehicleModelTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleModels::route('/'),
            'create' => Pages\CreateVehicleModel::route('/create'),
            'edit' => Pages\EditVehicleModel::route('/{record}/edit'),
        ];
    }

    // public static function canCreate(): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-model.create');
    // }

    // public static function canEdit(Model $record): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-model.update');
    // }   
    
    // public static function canDelete(Model $record): bool
    // {        
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-model.delete');
    // }  

}
