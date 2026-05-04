<?php

namespace App\Filament\Resources\WorkActivityReportResource\Tables;

use App\Jobs\Reports\ExportWorkActivityReportJob;
use App\Models\Reports\WorkActivityReport;
use App\Models\Snapshots\WorkTaskSubject;
use Carbon\CarbonInterval;
use Dpb\Departments\Services\DepartmentService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class WorkActivityReportTabe
{
    public static function make(Table $table): Table
    {
        // 1. Fetch unique types from your snapshot/subject table
        $subjectTypes = WorkTaskSubject::query()
            ->distinct()
            ->pluck('subject_type');

        $dynamicColumns = [];

        foreach ($subjectTypes as $type) {
            $dynamicColumns[] = Tables\Columns\TextColumn::make("subject_{$type}")
                ->label(fn() => match ($type) {
                    'vehicle' => 'Vozidlo',
                    'table' => 'Tabuľa',
                    default => $type
                })
                ->getStateUsing(function ($record) use ($type) {
                    // Find the specific subject for this activity and this column's type
                    return $record->taskSubjects
                        ->where('subject_type', $type)
                        ->pluck('subject_label')
                        ->join(', ');
                });
        }

        return $table
            ->heading(__('reports/work-activity-report.table.heading'))
            ->deferLoading()
            ->modifyQueryUsing(function (
                Builder $query,
                DepartmentService $departmentSvc
            ) {
                return $query
                    ->whereIn('department_code', $departmentSvc->getAvailableDepartments()->pluck('code'));
            })
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            // ->recordClasses(fn($record) => match ($record->state?->getValue()) {
            //     States\Inspection\Upcoming::$name => 'bg-blue-200',
            //     States\Inspection\InProgress::$name => 'bg-yellow-200',
            //     default => null,
            // })
            ->columns(array_merge([
                Tables\Columns\TextColumn::make('activity_date')
                    ->label(__('reports/work-activity-report.table.columns.activity_date'))
                    ->date('Y-m-d'),
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
                // activity subject label    
                // Tables\Columns\TextColumn::make('activity_subject_label')
                // Tables\Columns\TextColumn::make('taskSubjects')
                //     ->label(__('reports/work-activity-report.table.columns.activity_subject_label.label'))
                //     ->tooltip(__('reports/work-activity-report.table.columns.activity_subject_label.tooltip'))
                //     ->formatStateUsing(function (WorkActivityReport $record) {
                //         return $record->taskSubjects
                //             ->map(fn($subject) => "{$subject->subject_type}: {$subject->subject_label}")
                //             ->join(', ');
                //     }),

                Tables\Columns\TextColumn::make('task_group_title')
                    ->label(__('reports/work-activity-report.table.columns.task_group_title')),
                Tables\Columns\TextColumn::make('task_item_group_title')
                    ->label(__('reports/work-activity-report.table.columns.task_item_group_title')),
                Tables\Columns\TextColumn::make('activity_title')
                    ->label(__('reports/work-activity-report.table.columns.activity_title')),
                Tables\Columns\TextColumn::make('activity_expected_duration')
                    ->label(__('reports/work-activity-report.table.columns.activity_expected_duration.label'))
                    ->tooltip(__('reports/work-activity-report.table.columns.activity_expected_duration.tooltip'))
                    ->formatStateUsing(
                        fn($record): string => $record->activity_expected_duration >= 0
                            ? CarbonInterval::seconds($record->activity_expected_duration)->cascade()->format('%H:%I')
                            : CarbonInterval::seconds($record->activity_real_duration)->cascade()->format('%H:%I')
                    ),
                Tables\Columns\TextColumn::make('activity_real_duration')
                    ->label(__('reports/work-activity-report.table.columns.activity_real_duration.label'))
                    ->tooltip(__('reports/work-activity-report.table.columns.activity_real_duration.tooltip'))
                    ->formatStateUsing(fn($state): string => CarbonInterval::seconds($state)->cascade()->format('%H:%I')),
                Tables\Columns\TextColumn::make('activity_is_fulfilled')
                    ->label(__('reports/work-activity-report.table.columns.activity_is_fulfilled'))
                    ->formatStateUsing(
                        fn($state): string => match ($state) {
                            0 => 'Nie',
                            1 => 'Áno',
                            default => 'Nehodnotené',
                        }
                    ),
                // ->label(__('dispatch-report.table.columns.description.label'))
                //     ->formatStateUsing(fn($record) => Str::substr($record->description, 0, 30) . '...'),
                Tables\Columns\TextColumn::make('task_id')
                    ->label(__('reports/work-activity-report.table.columns.task_id'))
                    ->url(
                        fn($record) => $record->task_id
                            ? route('filament.admin.resources.task.task-assignments.edit', ['record' => $record->task_id])
                            : null
                    )
                    ->color(fn($state) => $state ? 'primary' : 'gray')
                    ->extraAttributes(fn($state) => [
                        'class' => $state ? 'underline cursor-pointer' : '',
                    ]),
                Tables\Columns\TextColumn::make('task_item_author_lastname')
                    ->label(__('reports/work-activity-report.table.columns.task_item_author_lastname'))
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ], $dynamicColumns))
            ->filters(WorkActivityReportTableFilter::make())
            ->headerActions([
                // Action::make('export')
                //     ->label('Export')
                //     ->icon('heroicon-o-arrow-down-tray')
                //     ->action(function ($livewire) {

                //         $filters = $livewire->getTableFiltersForm()->getState();

                //         $fileName = 'work_activity_' . now()->format('Ymd_His') . '.xlsx';

                //         ExportWorkActivityReportJob::dispatch(
                //             $filters,
                //             $fileName,
                //             auth()->id()
                //         );

                //         Notification::make()
                //             ->title(__('reports/export.export_started.title'))
                //             ->body(__('reports/export.export_started.body'))
                //             ->success()
                //             ->send();
                //     })
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
