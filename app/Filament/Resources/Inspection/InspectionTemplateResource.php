<?php

namespace App\Filament\Resources\Inspection;

use App\Filament\Imports\Inspection\InspectionTemplateImporter;
use App\Filament\Resources\Inspection\InspectionTemplateResource\Pages;
use App\Filament\Resources\Inspection\InspectionTemplateResource\RelationManagers;
use App\Filament\Resources\Inspection\InspectionTemplateResource\Tables\InspectionTemplateTable;
use App\Models\Fleet\VehicleModel;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionTemplateResource extends Resource
{
    protected static ?string $model = InspectionTemplate::class;

    public static function getModelLabel(): string
    {
        return __('inspections/inspection-template.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('inspections/inspection-template.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('inspections/inspection-template.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('inspections/inspection-template.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('title'),
                Forms\Components\Select::make('vehicleModels')
                    ->relationship('vehicleModels', 'title')
                    ->multiple()
                    ->preload()
                    ->searchable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return InspectionTemplateTable::make($table);
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
            'index' => Pages\ListInspectionTemplates::route('/'),
            'create' => Pages\CreateInspectionTemplate::route('/create'),
            'edit' => Pages\EditInspectionTemplate::route('/{record}/edit'),
        ];
    }
}
