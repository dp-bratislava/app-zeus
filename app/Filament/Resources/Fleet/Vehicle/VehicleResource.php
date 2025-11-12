<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Components\DepartmentPicker;
use App\Filament\Imports\Fleet\VehicleImporter;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Forms\VehicleForm;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Infolists\VehicleInfolist;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Pages;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\RelationManagers;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Tables\VehicleTable;
use App\Services\Fleet\VehicleService;
use App\StateTransitions\Fleet\Vehicle\DiscardedToInService;
use App\StateTransitions\Fleet\Vehicle\InServiceToDiscarded;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    public static function getModelLabel(): string
    {
        return __('fleet/vehicle.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/vehicle.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/vehicle.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/vehicle.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return VehicleForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return VehicleTable::make($table);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return VehicleInfolist::make($infolist);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\TicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'list-cards' => Pages\ListVehicleCards::route('/cards'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
            'view' => Pages\ViewVehicle::route('/{record}'),
        ];
    }
}
