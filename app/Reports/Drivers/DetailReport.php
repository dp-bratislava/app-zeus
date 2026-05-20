<?php

namespace App\Reports\Drivers;

use App\Filament\Exports\Reports\DetailReportExporter;
use App\Services\DateRangeValidator;
use App\Models\Reports\WorkActivityReport;
use App\Models\Snapshots\WorkTaskSubject;
use Carbon\CarbonInterval;
use Dpb\Departments\Services\DepartmentService;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Dpb\DatahubSync\Models\Department;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Components\DurationColumn;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

use Filament\Forms\Components\Placeholder;

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
                ->date('Y-m-d'),
            TextColumn::make('personal_id')
                ->label(__('reports/work-activity-report.table.columns.personal_id')),
            TextColumn::make('full_name')
                ->label(__('reports/work-activity-report.table.columns.full_name')),
            TextColumn::make('department_code')
                ->label(__('reports/work-activity-report.table.columns.department_code')),
            TextColumn::make('task_group_title')
                ->label(__('reports/work-activity-report.table.columns.task_group_title')),
            TextColumn::make('task_item_group_title')
                ->label(__('reports/work-activity-report.table.columns.task_item_group_title'))
                ->limit(20)
                ->tooltip(fn($record) => $record->task_item_group_title),
            TextColumn::make('activity_title')
                ->label(__('reports/work-activity-report.table.columns.activity_title'))
                ->limit(20)
                ->tooltip(fn($record) => $record->activity_title),
            TextColumn::make('activity_expected_duration')
                ->label(__('reports/work-activity-report.table.columns.activity_expected_duration.label'))
                ->tooltip(__('reports/work-activity-report.table.columns.activity_expected_duration.tooltip'))
                ->formatStateUsing(function ($record) {
                    // Determine which value to use
                    $seconds = $record->activity_expected_duration >= 0 
                        ? $record->activity_expected_duration 
                        : $record->activity_real_duration;

                    if (!$seconds) return '0:00';

                    $interval = CarbonInterval::seconds($seconds)->cascade();
                    return sprintf('%d:%02d', floor($interval->totalHours), $interval->minutes);
                }),
                
            DurationColumn::make('activity_real_duration')
                ->label(__('reports/work-activity-report.table.columns.activity_real_duration.label'))
                ->tooltip(__('reports/work-activity-report.table.columns.activity_real_duration.tooltip')),
            TextColumn::make('activity_is_fulfilled_label')
                ->label(__('reports/work-activity-report.table.columns.activity_is_fulfilled')),
            TextColumn::make('task_id')
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
            TextColumn::make('task_item_author_lastname')
                ->label(__('reports/work-activity-report.table.columns.task_item_author_lastname')),
        ], $dynamicColumns);
    }

    public function getFilters(): array
    {
        $validator = new DateRangeValidator(120);

        return [
            Filter::make('date_range')
                ->form([
                    DatePicker::make('date_from')
                        ->label('Dátum od')
                        ->default(now()->subDays(120)->format('Y-m-d')) // Prefills 120 days ago
                        ->live(onBlur: true),
                    DatePicker::make('date_to')
                        ->label('Dátum do')
                        ->default(now()->format('Y-m-d')) // Prefills today
                        ->live(onBlur: true),
                    Placeholder::make('date_error')
                        ->content(function ($get) use ($validator) {
                            $from = $get('date_from');
                            $to = $get('date_to');
                            $validation = $validator->validate($from, $to);

                            if (!$validation['isValid']) {
                                return new HtmlString('
                                    <span style="color: red">
                                        ' . $validation['error'] . '
                                    </span>
                                ');
                            }

                            return '';
                        })
                        ->hiddenLabel(),
                ])
                ->query(function (Builder $query, array $data) use ($validator): Builder {
                    // Validate the date range before querying
                    if ($data['date_from'] && $data['date_to']) {
                        if (!$validator->validate($data['date_from'], $data['date_to'])['isValid']) {
                            return $query->whereRaw('1 = 0');
                        }
                    }

                    return $query
                        ->when(
                            $data['date_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('activity_date', '>=', $date)
                        )
                        ->when(
                            $data['date_to'],
                            fn(Builder $query, $date): Builder => $query->whereDate('activity_date', '<=', $date)
                        );
                })
                ->columns(3),

            // department
            SelectFilter::make('department')
                ->label(__('reports/work-activity-report.table.filters.department'))
                ->options(fn(DepartmentService $departmentSvc) => Department::whereIn('id', $departmentSvc->getAvailableDepartments()->pluck('id'))->pluck('code', 'code'))
                ->multiple()
                ->searchable()
                ->attribute('department_code'),

            // is fulfilled
        ];
    }

    public function getExporter(): string
    {
        return DetailReportExporter::class;
    }

    public function generateExportFilename(): string
    {
        return 'work_activity_' . now()->format('Ymd_His') . '.xlsx';
    }

    public function applyQueryModifications(Builder $query): Builder
    {
        return $query
            ->whereIn('department_code', $this->departmentService->getAvailableDepartments()->pluck('code'))
            ->with('taskSubjects');
    }
}
