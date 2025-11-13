<?php

namespace App\Filament\Resources\Inspection;

use App\Filament\Resources\Inspection\InspectionTemplateResource\Forms\InspectionTemplateForm;
use App\Filament\Resources\Inspection\InspectionTemplateResource\Pages;
use App\Filament\Resources\Inspection\InspectionTemplateResource\Tables\InspectionTemplateTable;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

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

    public static function getNavigationSort(): ?int
    {
        return config('pkg-inspections.navigation.inspection-template') ?? 999;
    }    

    public static function form(Form $form): Form
    {
        return InspectionTemplateForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return InspectionTemplateTable::make($table);
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
