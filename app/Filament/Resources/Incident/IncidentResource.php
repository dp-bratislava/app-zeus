<?php

namespace App\Filament\Resources\Incident;

use App\Filament\Resources\Incident\IncidentResource\Forms\IncidentForm;
use App\Filament\Resources\Incident\IncidentResource\Pages;
use App\Filament\Resources\Incident\IncidentResource\Tables\IncidentTable;
use App\States;
use Carbon\Carbon;
use Dpb\Package\Incidents\Models\Incident;
use Dpb\Package\Incidents\Models\IncidentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    public static function getModelLabel(): string
    {
        return __('incidents/incident.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('incidents/incident.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('incidents/incident.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('incidents/incident.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return IncidentForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return IncidentTable::make($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }
}
