<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DispatchReportResource\Forms\DispatchReportForm;
use App\Filament\Resources\DispatchReportResource\Pages;
use App\Filament\Resources\DispatchReportResource\RelationManagers;
use App\Filament\Resources\DispatchReportResource\Tables\DispatchReportTable;
use App\Models\DispatchReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DispatchReportResource extends Resource
{
    protected static ?string $model = DispatchReport::class;

    public static function getModelLabel(): string
    {
        return __('dispatch-report.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dispatch-report.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('dispatch-report.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dispatch-report.navigation.group');
    }
    
    public static function form(Form $form): Form
    {
        return DispatchReportForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return DispatchReportTable::make($table) ;
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
            'index' => Pages\ListDispatchReports::route('/'),
            'create' => Pages\CreateDispatchReport::route('/create'),
            'edit' => Pages\EditDispatchReport::route('/{record}/edit'),
        ];
    }
}
