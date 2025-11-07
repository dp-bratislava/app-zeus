<?php

namespace App\Filament\Resources\Inspection;

use App\Filament\Resources\Inspection\InspectionTemplateGroupResource\Pages;
use App\Filament\Resources\Inspection\InspectionTemplateGroupResource\RelationManagers;
use App\Filament\Resources\Inspection\InspectionTemplateGroupResource\Tables\InspectionTemplateGroupTable;
use Dpb\Package\Inspections\Models\InspectionTemplateGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionTemplateGroupResource extends Resource
{
    protected static ?string $model = InspectionTemplateGroup::class;

    public static function getModelLabel(): string
    {
        return __('inspections/inspection-template-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('inspections/inspection-template-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('inspections/inspection-template-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('inspections/inspection-template-group.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return InspectionTemplateGroupTable::make($table);
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
            'index' => Pages\ListInspectionTemplateGroups::route('/'),
            'create' => Pages\CreateInspectionTemplateGroup::route('/create'),
            'edit' => Pages\EditInspectionTemplateGroup::route('/{record}/edit'),
        ];
    }
}
