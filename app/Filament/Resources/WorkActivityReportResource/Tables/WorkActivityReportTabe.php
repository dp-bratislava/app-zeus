<?php

namespace App\Filament\Resources\WorkActivityReportResource\Tables;

use App\Jobs\Reports\ExportWorkActivityReportJob;
use Carbon\CarbonInterval;
use Dpb\Departments\Services\DepartmentService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkActivityReportTabe
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('reports/work-activity-report.table.heading'))
            ->deferLoading()
            ->modifyQueryUsing(function (
                Builder $query,
                DepartmentService $departmentSvc
            ) {
                return $query
                    ->whereIn('department_id', $departmentSvc->getAvailableDepartments()->pluck('id'));
            })
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            // ->recordClasses(fn($record) => match ($record->state?->getValue()) {
            //     States\Inspection\Upcoming::$name => 'bg-blue-200',
            //     States\Inspection\InProgress::$name => 'bg-yellow-200',
            //     default => null,
            // })
            ->columns([
                Tables\Columns\TextColumn::make('activity_date')
                    ->label(__('reports/work-activity-report.table.columns.activity_date'))
                    ->date('j.n.Y'),
                // ->label(__('dispatch-report.table.columns.date.label')),
                Tables\Columns\TextColumn::make('personal_id')
                    ->label(__('reports/work-activity-report.table.columns.personal_id')),
                Tables\Columns\TextColumn::make('last_name')
                    ->label(__('reports/work-activity-report.table.columns.full_name'))
                    ->formatStateUsing(fn($record): string => "{$record->last_name} {$record->first_name}"),
                // Tables\Columns\TextColumn::make('last_name')
                //     ->label(__('reports/work-activity-report.table.columns.last_name')),
                // Tables\Columns\TextColumn::make('first_name')
                //     ->label(__('reports/work-activity-report.table.columns.first_name')),
                Tables\Columns\TextColumn::make('department_code')
                    ->label(__('reports/work-activity-report.table.columns.department_code')),
                Tables\Columns\TextColumn::make('subject_code')
                    ->label(__('reports/work-activity-report.table.columns.subject_code.label'))
                    ->tooltip(__('reports/work-activity-report.table.columns.subject_code.tooltip')),
                Tables\Columns\TextColumn::make('task_group_title')
                    ->label(__('reports/work-activity-report.table.columns.task_group_title')),
                Tables\Columns\TextColumn::make('task_item_group_title')
                    ->label(__('reports/work-activity-report.table.columns.task_item_group_title')),
                Tables\Columns\TextColumn::make('wtf_task_title')
                    ->label(__('reports/work-activity-report.table.columns.wtf_task_title')),
                Tables\Columns\TextColumn::make('expected_duration')
                    ->label(__('reports/work-activity-report.table.columns.expected_duration'))
                    ->formatStateUsing(
                        fn($record): string => $record->expected_duration >= 0
                            ? CarbonInterval::seconds($record->expected_duration)->cascade()->format('%H:%I')
                            : CarbonInterval::seconds($record->real_duration)->cascade()->format('%H:%I')
                    ),
                Tables\Columns\TextColumn::make('real_duration')
                    ->label(__('reports/work-activity-report.table.columns.real_duration'))
                    ->formatStateUsing(fn($state): string => CarbonInterval::seconds($state)->cascade()->format('%H:%I')),
                Tables\Columns\TextColumn::make('is_fulfilled')
                    ->label(__('reports/work-activity-report.table.columns.is_fulfilled'))
                    ->formatStateUsing(
                        fn($state): string => match ($state) {
                            0 => 'Nie',
                            1 => 'Áno',
                            default => 'Nehodnotené',
                        }
                    ),
                // ->label(__('dispatch-report.table.columns.description.label'))
                //     ->formatStateUsing(fn($record) => Str::substr($record->description, 0, 30) . '...'),
                // Tables\Columns\TextColumn::make('state')
                //     ->label(__('dispatch-report.table.columns.state.label')),
                Tables\Columns\TextColumn::make('task_item_author_lastname')
                    ->label(__('reports/work-activity-report.table.columns.task_item_author_lastname'))
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters(WorkActivityReportTableFilter::make())
            ->headerActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {

                        $filters = $livewire->getTableFiltersForm()->getState();

                        $fileName = 'work_activity_' . now()->format('Ymd_His') . '.xlsx';

                        ExportWorkActivityReportJob::dispatch(
                            $filters,
                            $fileName,
                            auth()->id()
                        );

                        Notification::make()
                            ->title(__('reports/export.export_started.title'))
                            ->body(__('reports/export.export_started.body'))
                            ->success()
                            ->send();
                    })
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
