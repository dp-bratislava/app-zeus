<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportsResource\Pages\ListReports;
use App\Models\Reports\WorkActivityReport;
use App\Reports\ReportFactory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;

class ReportsResource extends Resource
{
    public static ?string $model = WorkActivityReport::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('reports/work-activity-report.navigation.label');
    }

    public static function table(Table $table): Table
    {
        // Get the report type from the Livewire component (page) instead of request
        $livewire = $table->getLivewire();
        $reportType = $livewire->currentReportType ?? request()->query('report');

        $driver = ReportFactory::make($reportType);

        return $table
            ->query($driver->getQuery())
            ->deferLoading()
            ->modifyQueryUsing(function ($query) use ($driver) {
                return $driver->applyQueryModifications($query);
            })
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(100)
            ->columns($driver->getColumns())
            ->filters($driver->getFilters())
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('id', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReports::route('/'),
        ];
    }
}
