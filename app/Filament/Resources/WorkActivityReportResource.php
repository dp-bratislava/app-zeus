<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkActivityReportResource\Pages;
use App\Filament\Resources\WorkActivityReportResource\Tables\WorkActivityReportTabe;
use App\Models\Reports\WorkActivityReport;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class WorkActivityReportResource extends Resource
{
    protected static ?string $model = WorkActivityReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return WorkActivityReportTabe::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkActivityReports::route('/'),
        ];
    }
}
