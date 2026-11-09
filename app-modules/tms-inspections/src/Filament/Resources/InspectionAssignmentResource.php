<?php

namespace Dpb\Modules\Inspections\Filament\Resources;

use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Schemas\InspectionAssignmentFrom;
use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Pages;
use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Tables\InspectionAssignmentTable;
use Dpb\Package\TaskMS\Models\InspectionAssignment;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class InspectionAssignmentResource extends Resource
{
    protected static ?string $model = InspectionAssignment::class;

    public static function getModelLabel(): string
    {
        return __('dpb-mod-inspections::inspection-assignment.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dpb-mod-inspections::inspection-assignment.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('dpb-mod-inspections::inspection-assignment.navigation.label');
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return __('dpb-mod-inspections::inspection-assignment.navigation.group');
    // }

    // public static function getNavigationSort(): ?int
    // {
    //     return config('pkg-inspections.navigation.inspection') ?? 999;
    // }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->can('inspections.inspection.read');
    // }

    public static function form(Schema $schema): Schema
    {
        return InspectionAssignmentFrom::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InspectionAssignmentTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInspectionAssignments::route('/'),
            'create' => Pages\CreateInspectionAssignment::route('/create'),
            'edit' => Pages\EditInspectionAssignment::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         // ->byVehicleType('A');
    //         ->byMaintenanceGroup('1TPA');
    // }
}
