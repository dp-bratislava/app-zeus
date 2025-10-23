<?php

namespace App\Filament\Resources\Inspection;

use App\Filament\Resources\Inspection\DailyMaintenanceResource\Forms\DailyMaintenanceForm;
use App\Filament\Resources\Inspection\DailyMaintenanceResource\Pages;
use App\Filament\Resources\Inspection\DailyMaintenanceResource\Tables\DailyMaintenanceTable;
use App\Models\Inspection\DailyMaintenance;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DailyMaintenanceResource extends Resource
{
    protected static ?string $model = Inspection::class;

    public static function getModelLabel(): string
    {
        return __('inspections/daily-maintenance.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('inspections/daily-maintenance.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('inspections/daily-maintenance.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('inspections/daily-maintenance.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return DailyMaintenanceForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return DailyMaintenanceTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyMaintenances::route('/'),
            // 'create' => Pages\CreateDailyMaintenance::route('/create'),
            'edit' => Pages\EditDailyMaintenance::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->byTemplateGroup('daily-maintenance');
    }    
}
