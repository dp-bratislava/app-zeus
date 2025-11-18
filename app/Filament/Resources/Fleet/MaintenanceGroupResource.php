<?php

namespace App\Filament\Resources\Fleet;

use App\Filament\Resources\Fleet\MaintenanceGroupResource\Pages;
use App\Filament\Resources\Fleet\MaintenanceGroupResource\Forms\MaintenanceGroupForm;
use App\Filament\Resources\Fleet\MaintenanceGroupResource\Tables\MaintenanceGroupTable;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class MaintenanceGroupResource extends Resource
{
    protected static ?string $model = MaintenanceGroup::class;

    public static function getModelLabel(): string
    {
        return __('fleet/maintenance-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/maintenance-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/maintenance-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/maintenance-group.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-fleet.navigation.maintenance-group') ?? 999;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('fleet.maintenance-group.read');
    }

    public static function form(Form $form): Form
    {
        return MaintenanceGroupForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceGroupTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceGroups::route('/'),
            'create' => Pages\CreateMaintenanceGroup::route('/create'),
            'edit' => Pages\EditMaintenanceGroup::route('/{record}/edit'),
        ];
    }

    // public static function canCreate(): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.maintenance-group.create');
    // }

    // public static function canEdit(Model $record): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.maintenance-group.update');
    // }   
    
    // public static function canDelete(Model $record): bool
    // {        
    //     return auth()->check() && auth()->user()->can('fleet.maintenance-group.delete');
    // }    

}
