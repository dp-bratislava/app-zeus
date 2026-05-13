<?php

namespace App\Reports\Drivers;

use App\Jobs\Reports\ExportDetailReportJob;
use App\Models\Reports\WorkActivityReport;
use App\Models\Snapshots\ReportSyncState;
use App\Models\Snapshots\WorkTaskSubject;
use Carbon\CarbonInterval;
use Dpb\Departments\Services\DepartmentService;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Dpb\DatahubSync\Models\Department;
use Filament\Tables\Columns\TextColumn;

class DetailReport implements ReportDriver
{
    private ?DepartmentService $departmentService = null;

    public function __construct()
    {
        $this->departmentService = app(DepartmentService::class);
    }

    public function key(): string
    {
        return 'work-activity';
    }

    public function name(): string
    {
        return __('reports/work-activity-report.navigation.label');
    }

    public function getQuery(): Builder
    {
        return WorkActivityReport::query();
    }

    public function getColumns(): array
    {
        $latestSync = ReportSyncState::byReportName('work-activity')->first();

        // 1. Fetch unique types from your snapshot/subject table
        $subjectTypes = WorkTaskSubject::query()
            ->whereHas('activity', function ($query) {
                $query->whereIn(
                    'department_code',
                    $this->departmentService->getAvailableDepartments()->pluck('code')
                );
            })
            ->distinct()
            ->pluck('subject_type');

        $dynamicColumns = [];

        foreach ($subjectTypes as $type) {
            $dynamicColumns[] = TextColumn::make("subject_{$type}")
                ->label(fn() => match ($type) {
                    'vehicle' => 'Vozidlo',
                    'table' => 'Tabuľa',
                    default => $type
                })
                ->getStateUsing(function ($record) use ($type) {
                    return $record->taskSubjects
                        ->where('subject_type', $type)
                        ->pluck('subject_label')
                        ->join(', ');
                });
        }

        return array_merge([
            TextColumn::make('activity_date')
                ->label(__('reports/work-activity-report.table.columns.activity_date'))
                ->toggleable()
                ->date('Y-m-d'),
            TextColumn::make('personal_id')
                ->label(__('reports/work-activity-report.table.columns.personal_id'))
                ->toggleable(),
            TextColumn::make('last_name')
                ->label(__('reports/work-activity-report.table.columns.full_name'))
                ->toggleable()
                ->formatStateUsing(fn($record): string => "{$record->last_name} {$record->first_name}"),
            TextColumn::make('department_code')
                ->label(__('reports/work-activity-report.table.columns.department_code'))
                ->toggleable(),
            TextColumn::make('task_group_title')
                ->label(__('reports/work-activity-report.table.columns.task_group_title'))
                ->toggleable(),
            TextColumn::make('task_item_group_title')
                ->label(__('reports/work-activity-report.table.columns.task_item_group_title'))
                ->toggleable(),
            TextColumn::make('activity_title')
                ->label(__('reports/work-activity-report.table.columns.activity_title'))
                ->toggleable(),
            TextColumn::make('activity_expected_duration')
                ->label(__('reports/work-activity-report.table.columns.activity_expected_duration.label'))
                ->toggleable()
                ->tooltip(__('reports/work-activity-report.table.columns.activity_expected_duration.tooltip'))
                ->formatStateUsing(
                    fn($record): string => $record->activity_expected_duration >= 0
                        ? CarbonInterval::seconds($record->activity_expected_duration)->cascade()->format('%H:%I')
                        : CarbonInterval::seconds($record->activity_real_duration)->cascade()->format('%H:%I')
                ),
            TextColumn::make('activity_real_duration')
                ->label(__('reports/work-activity-report.table.columns.activity_real_duration.label'))
                ->toggleable()
                ->tooltip(__('reports/work-activity-report.table.columns.activity_real_duration.tooltip'))
                ->formatStateUsing(fn($state): string => CarbonInterval::seconds($state)->cascade()->format('%H:%I')),
            TextColumn::make('activity_is_fulfilled_label')
                ->label(__('reports/work-activity-report.table.columns.activity_is_fulfilled'))
                ->toggleable(),
            TextColumn::make('task_id')
                ->label(__('reports/work-activity-report.table.columns.task_id'))
                ->toggleable()
                ->url(
                    fn($record) => $record->task_id
                        ? route('filament.admin.resources.task.task-assignments.edit', ['record' => $record->task_id])
                        : null
                )
                ->color(fn($state) => $state ? 'primary' : 'gray')
                ->extraAttributes(fn($state) => [
                    'class' => $state ? 'underline cursor-pointer' : '',
                ]),
            TextColumn::make('task_item_author_lastname')
                ->label(__('reports/work-activity-report.table.columns.task_item_author_lastname'))
                ->toggleable()
                ->toggledHiddenByDefault(),
        ], $dynamicColumns);
    }

    public function getFilters(): array
    {
        return [
            Tables\Filters\Filter::make('activity_date')
                ->form([
                    DatePicker::make('activity_date_from')
                        ->label(__('reports/work-activity-report.table.filters.date_from')),
                    DatePicker::make('activity_date_to')
                        ->label(__('reports/work-activity-report.table.filters.date_to')),

                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['activity_date_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('activity_date', '>=', $date)
                        )
                        ->when(
                            $data['activity_date_to'],
                            fn(Builder $query, $date): Builder => $query->whereDate('activity_date', '<=', $date)
                        );
                })->columns(2),

            // department
            Tables\Filters\SelectFilter::make('department')
                ->label(__('reports/work-activity-report.table.filters.department'))
                ->options(fn(DepartmentService $departmentSvc) => Department::whereIn('id', $departmentSvc->getAvailableDepartments()->pluck('id'))->pluck('code', 'code'))
                ->multiple()
                ->attribute('department_code'),

            // is fulfilled
            Tables\Filters\SelectFilter::make('is_fulfilled_label')
                ->label(__('reports/work-activity-report.table.filters.activity_is_fulfilled'))
                ->options([
                    'Nevyhodnotené' => 'Nevyhodnotené',
                    'Nie' => 'Nie',
                    'Áno' => 'Áno',
                ])
                ->multiple()
                ->attribute('activity_is_fulfilled_label'),
            ];
    }

    public function getExportJobClass(): string
    {
        return ExportDetailReportJob::class;
    }

    public function applyQueryModifications(Builder $query): Builder
    {
        return $query
            ->whereIn('department_code', $this->departmentService->getAvailableDepartments()->pluck('code'))
            ->with('taskSubjects');
    }

    public function getExportColumns(): array
    {
        return [
            ['key' => 'department_code', 'label' => 'Stredisko'],
            ['key' => 'task_created_at', 'label' => 'Čas vytvorenia zákazky'],
            ['key' => 'activity_is_fulfilled_label', 'label' => 'Splnené'],
            ['key' => 'task_date', 'label' => 'Dátum zákazky', 'type' => 'date'],
            ['key' => 'task_group_title', 'label' => 'Typ zákazky'],
            ['key' => 'task_assigned_to_label', 'label' => 'Prevádzka zákazky'],
            ['key' => 'task_author_lastname', 'label' => 'Zákzaku vytvoril'],
            ['key' => 'task_item_group_title', 'label' => 'Typ podzákazky'],
            ['key' => 'task_item_assigned_to_label', 'label' => 'Prevádzka podzákazky'],
            ['key' => 'task_item_author_lastname', 'label' => 'Podzákazku vytvoril'],
            ['key' => 'wtf_task_created_at', 'label' => 'Čas priradenia práce'],
            ['key' => 'activity_date', 'label' => 'Dátum výkonu práce', 'type' => 'date'],
            ['key' => 'personal_id', 'label' => 'Osobné číslo'],
            ['key' => 'last_name', 'label' => 'Priezvisko'],
            ['key' => 'first_name', 'label' => 'Meno'],
            ['key' => 'activity_title', 'label' => 'Norma', 'width' => 35],
            ['key' => 'activity_expected_duration', 'label' => 'Norma trvanie', 'type' => 'duration'],
            ['key' => 'activity_real_duration', 'label' => 'Reálne trvanie', 'type' => 'duration'],
        ];
    }

    public function buildExportFilters(array $filters): array
    {
        return $filters;
    }
}
