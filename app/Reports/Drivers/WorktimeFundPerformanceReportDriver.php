<?php

namespace App\Reports\Drivers;

use App\Jobs\Reports\ExportWorktimeFundReportJob;
use App\Models\Reports\WorktimeFundPerformanceReport;
use Dpb\Departments\Services\DepartmentService;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

class WorktimeFundPerformanceReportDriver implements ReportDriver
{
    private ?DepartmentService $departmentService = null;

    public function __construct()
    {
        $this->departmentService = app(DepartmentService::class);
    }

    public function key(): string
    {
        return 'worktime-fund-performance';
    }

    public function name(): string
    {
        return 'Worktime Fund Performance';
    }

    public function icon(): string
    {
        return 'heroicon-o-chart-bar';
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
            Tables\Columns\TextColumn::make('stredisko')
                ->label('Department')
                ->toggleable(),
            Tables\Columns\TextColumn::make('osob_cislo')
                ->label('Personal ID')
                ->toggleable(),
            Tables\Columns\TextColumn::make('meno')
                ->label('Name')
                ->toggleable(),
            Tables\Columns\TextColumn::make('suma_cas_skutocny')
                ->label('Actual Time')
                ->toggleable(),
            Tables\Columns\TextColumn::make('suma_cas_norma')
                ->label('Expected Time')
                ->toggleable(),
            Tables\Columns\TextColumn::make('plnenie')
                ->label('Performance %')
                ->toggleable(),
        ];
    }

    public function getFilters(): array
    {
        return [
            Tables\Filters\Filter::make('date')
                ->form([
                    Forms\Components\DatePicker::make('date_from')
                        ->label('From Date'),
                    Forms\Components\DatePicker::make('date_to')
                        ->label('To Date'),
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
                }),
        ];
    }

    public function getExportJobClass(): string
    {
        return ExportWorktimeFundReportJob::class;
    }

    public function getExportTemplates(): array
    {
        return [
            'default' => 'Default Template',
        ];
    }

    public function applyQueryModifications(Builder $query): Builder
    {
        return $query->whereIn('d.code', $this->departmentService->getAvailableDepartments()->pluck('code'));
    }

    public function getExportColumns(): array
    {
        return [
            ['key' => 'stredisko', 'label' => 'Department'],
            ['key' => 'osob_cislo', 'label' => 'Personal ID'],
            ['key' => 'meno', 'label' => 'Name'],
            ['key' => 'suma_cas_skutocny', 'label' => 'Actual Time'],
            ['key' => 'suma_cas_norma', 'label' => 'Expected Time'],
            ['key' => 'plnenie', 'label' => 'Performance %'],
        ];
    }

    public function buildExportFilters(array $filters): array
    {
        return $filters;
    }
}
