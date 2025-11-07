<?php

namespace App\Filament\Resources\Inspection;

use App\Filament\Resources\Inspection\InspectionResource\Forms\InspectionFrom;
use App\Filament\Resources\Inspection\InspectionResource\Pages;
use App\Filament\Resources\Inspection\InspectionResource\RelationManagers;
use App\Filament\Resources\Inspection\InspectionResource\Tables\InspectionTable;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    public static function getModelLabel(): string
    {
        return __('inspections/inspection.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('inspections/inspection.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('inspections/inspection.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('inspections/inspection.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return InspectionFrom::make($form);
    }

    public static function table(Table $table): Table
    {
        return InspectionTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInspections::route('/'),
            'create' => Pages\CreateInspection::route('/create'),
            'edit' => Pages\EditInspection::route('/{record}/edit'),
        ];
    }
}
