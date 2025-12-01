<?php

namespace App\Filament\Resources\Incident;

use App\Filament\Resources\Incident\IncidentTypeResource\Forms\IncidentTypeForm;
use App\Filament\Resources\Incident\IncidentTypeResource\Forms\IncidentTypeTable;
use App\Filament\Resources\Incident\IncidentTypeResource\Pages;
use App\Filament\Resources\Incident\IncidentTypeResource\Tables\IncidentTypeTable as TablesIncidentTypeTable;
use Dpb\Package\Incidents\Models\IncidentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncidentTypeResource extends Resource
{
    protected static ?string $model = IncidentType::class;

    public static function getModelLabel(): string
    {
        return __('incidents/incident-type.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('incidents/incident-type.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('incidents/incident-type.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('incidents/incident-type.navigation.group');
    }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->can('incidents.incident-type.read');
    // }    

    public static function form(Form $form): Form
    {
        return IncidentTypeForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return TablesIncidentTypeTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidentTypes::route('/'),
            'create' => Pages\CreateIncidentType::route('/create'),
            'edit' => Pages\EditIncidentType::route('/{record}/edit'),
        ];
    }
}
