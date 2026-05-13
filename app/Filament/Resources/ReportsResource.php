<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportsResource\Pages;
use App\Models\Reports\WorkActivityReport;
use App\Reports\ReportFactory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ReportsResource extends Resource
{
    protected static ?string $model = WorkActivityReport::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('reports/work-activity-report.navigation.label');
    }

    public static function table(Table $table): Table
    {
        $reportType = request()->query('report', 'work-activity');
        $driver = ReportFactory::make($reportType);

        return $table
            ->heading(__('reports/work-activity-report.table.heading'))
            ->description(__('reports/work-activity-report.table.description', ['latest-sync' => now()]))
            ->deferLoading()
            ->modifyQueryUsing(function ($query) use ($driver) {
                return $driver->applyQueryModifications($query);
            })
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns($driver->getColumns())
            ->filters($driver->getFilters())
            ->headerActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) use ($driver) {
                        $filters = $livewire->getTableFiltersForm()->getState();

                        $fileName = 'work_activity_' . now()->format('Ymd_His') . '.xlsx';

                        $jobClass = $driver->getExportJobClass();
                        $jobClass::dispatch(
                            $filters,
                            $fileName,
                            auth()->id(),
                        );

                        Notification::make()
                            ->title(__('reports/export.export_started.title'))
                            ->body(__('reports/export.export_started.body'))
                            ->success()
                            ->send();
                    })
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
        ];
    }
}
