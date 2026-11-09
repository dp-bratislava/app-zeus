<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections;

use Dpb\Package\Inspections\Models\InspectionTemplateGroup;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource\Forms\InspectionTempalteGroupFrom;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource\Pages;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource\Tables\InspectionTemplateGroupTable;
use Dpb\Modules\Inspections\Traits\HasConfigNavigationSort;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class InspectionTemplateGroupResource extends Resource
{
    use HasConfigNavigationSort;

    protected static ?string $model = InspectionTemplateGroup::class;

    public static function getModelLabel(): string
    {
        return __('dpb-mod-inspections::inspection-template-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dpb-mod-inspections::inspection-template-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('dpb-mod-inspections::inspection-template-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dpb-mod-inspections::inspection-template-group.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return static::navigationSortFromConfig('inspection-template-group', 'enums');
        // return config('pkg-task-ms-ui.navigation.inspection-template-group') ?? 999;
    }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->can('inspections.inspection-template-group.read');
    // }

    public static function form(Schema $schema): Schema
    {
        return InspectionTempalteGroupFrom::make($schema);
    }

    public static function table(Table $table): Table
    {
        return InspectionTemplateGroupTable::make($table);
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
