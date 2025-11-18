<?php

namespace App\Filament\Resources\Inspection;

use App\Filament\Resources\Inspection\UpcomingInspectionResource\Pages;
use App\Filament\Resources\Inspection\UpcomingInspectionResource\Tables\UpcomingInspectionTable;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UpcomingInspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    public static function getModelLabel(): string
    {
        return __('inspections/upcoming-inspection.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('inspections/upcoming-inspection.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('inspections/upcoming-inspection.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('inspections/upcoming-inspection.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-inspections.navigation.upcomming-inspection') ?? 999;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('inspections.upcomming-inspection.read');
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
        return UpcomingInspectionTable::make($table);
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
            'index' => Pages\ListUpcomingInspections::route('/'),
            // 'create' => Pages\CreateUpcomingInspection::route('/create'),
            // 'edit' => Pages\EditUpcomingInspection::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('state', 'upcoming');
    }
}
