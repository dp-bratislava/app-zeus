<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections;

use Dpb\Package\TaskMS\Models\InspectionAssignment;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\UpcomingInspectionResource\Pages;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\UpcomingInspectionResource\Tables\UpcomingInspectionAssignmentTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UpcomingInspectionResource extends Resource
{
    protected static ?string $model = InspectionAssignment::class;

    public static function getModelLabel(): string
    {
        return __('dpb-mod-inspections::upcoming-inspection.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dpb-mod-inspections::upcoming-inspection.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('dpb-mod-inspections::upcoming-inspection.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dpb-mod-inspections::upcoming-inspection.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-inspections.navigation.upcomming-inspection') ?? 999;
    }

    public static function canViewAny(): bool
    {
        // return auth()->user()->can('inspections.upcomming-inspection.read');
        return false;
    }

    // public static function form(Schema $schema): Schema
    // {
    //     return $form
    //         ->schema([
    //             //
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return UpcomingInspectionAssignmentTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUpcomingInspections::route('/'),
            // 'create' => Pages\CreateUpcomingInspection::route('/create'),
            // 'edit' => Pages\EditUpcomingInspection::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('inspection', function ($q) {
                $q->where('state', '=', 'upcoming');
            });
    }
}
