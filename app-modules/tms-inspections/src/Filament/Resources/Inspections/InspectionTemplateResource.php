<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections;

use Dpb\Package\Inspections\Models\InspectionTemplate;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Forms\InspectionTemplateForm;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Pages;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Tables\InspectionTemplateTable;
use Dpb\Modules\Inspections\Traits\HasConfigNavigationSort;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class InspectionTemplateResource extends Resource
{
    use HasConfigNavigationSort;

    protected static ?string $model = InspectionTemplate::class;

    public static function getModelLabel(): string
    {
        return __('dpb-mod-inspections::inspection-template.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dpb-mod-inspections::inspection-template.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('dpb-mod-inspections::inspection-template.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dpb-mod-inspections::inspection-template.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return static::navigationSortFromConfig('inspection-template', 'enums');
        // return config('pkg-task-ms-ui.navigation.inspection-template') ?? 999;
    }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->can('inspections.inpection-template.read');
    // }

    public static function form(Schema $schema): Schema
    {
        return InspectionTemplateForm::make($schema);
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
