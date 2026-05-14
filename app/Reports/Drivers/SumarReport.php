<?php

namespace App\Reports\Drivers;


use App\Filament\Exports\Reports\SumarReportExporter;
use App\Models\Reports\WorktimeFundPerformanceReport;
use Dpb\Departments\Services\DepartmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Dpb\DatahubSync\Models\Department;

class SumarReport implements ReportDriver
{
    private ?DepartmentService $departmentService = null;

    public function __construct()
    {
        $this->departmentService = app(DepartmentService::class);
    }

    public function key(): string
    {
        return 'sumar';
    }

    public function name(): string
    {
        return 'Sumár pracovníkov';
    }

    public function getQuery(): Builder
    {
        return WorktimeFundPerformanceReport::query()
            ->select([
                'd.code as stredisko',
                new Expression("
                    ROW_NUMBER() OVER (ORDER BY c.pid) as id
                "),
                new Expression("TRIM(LEADING '0' FROM c.pid) as osob_cislo"),
                new Expression("CONCAT(wt.last_name, ' ', wt.first_name) AS meno"),
                new Expression("TIME_FORMAT(SEC_TO_TIME(SUM(dpb_worktimefund_model_activityrecord.real_duration)), '%H:%i') AS suma_cas_skutocny"),
                new Expression("TIME_FORMAT(SEC_TO_TIME(SUM(dpb_worktimefund_model_activityrecord.expected_duration)), '%H:%i') AS suma_cas_norma"),
                new Expression("ROUND(100 * SUM(dpb_worktimefund_model_activityrecord.expected_duration) / SUM(dpb_worktimefund_model_activityrecord.real_duration), 0) AS plnenie"),
            ])
            ->leftJoin('dpb_worktimefund_model_worktime as wt', 'wt.id', '=', 'dpb_worktimefund_model_activityrecord.parent_id')
            ->leftJoin('datahub_employee_contracts as c', 'c.pid', '=', 'wt.personal_id')
            ->leftJoin('datahub_departments as d', 'd.id', '=', 'c.datahub_department_id')
            ->where('dpb_worktimefund_model_activityrecord.type', 'O')
            ->groupBy('c.pid', 'wt.last_name', 'wt.first_name', 'd.code');
    }

    public function getColumns(): array
    {
        return [
            TextColumn::make('stredisko')
                ->label('stredisko'),
            TextColumn::make('osob_cislo')
                ->label('osob_cislo'),
            TextColumn::make('meno')
                ->label('meno'),
            TextColumn::make('suma_cas_skutocny')
                ->label('suma_cas_skutocny'),
            TextColumn::make('suma_cas_norma')
                ->label('suma_cas_norma'),
            TextColumn::make('plnenie')
                ->label('plnenie'),
        ];
    }

    public function getFilters(): array
    {
        return [
            Filter::make('date')
                ->form([
                    DatePicker::make('date_from')
                        ->label('Dátum od'),
                    DatePicker::make('date_to')
                        ->label('Dátum do'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['date_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('dpb_worktimefund_model_activityrecord.date', '>=', $date),
                        )
                        ->when(
                            $data['date_to'],
                            fn (Builder $query, $date): Builder => $query->whereDate('dpb_worktimefund_model_activityrecord.date', '<=', $date),
                        );
                })->columns(2),

            SelectFilter::make('department')
                ->label(__('reports/work-activity-report.table.filters.department'))
                ->options(fn(DepartmentService $departmentSvc) => Department::whereIn('id', $departmentSvc->getAvailableDepartments()->pluck('id'))->pluck('code', 'code'))
                ->multiple()
                ->query(function (Builder $query, array $data): Builder {
            return $query->when(
                $data['values'],
                fn (Builder $query, $values): Builder => $query->whereIn('d.code', $values)
            );
        }),
        ];
    }

    public function getExporter(): string
    {
        return SumarReportExporter::class;
    }

    public function generateExportFilename(): string
    {
        return 'sumar_' . now()->format('Ymd_His') . '.xlsx';
    }

    public function applyQueryModifications(Builder $query): Builder
    {
        return $query->whereIn('d.code', $this->departmentService->getAvailableDepartments()->pluck('code'));
    }
}
