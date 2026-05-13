<?php

namespace App\Reports\Drivers;

use App\Jobs\Reports\ExportSumarReportJob;
use App\Models\Reports\WorktimeFundPerformanceReport;
use Dpb\Departments\Services\DepartmentService;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

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
            Tables\Columns\TextColumn::make('stredisko')
                ->label('stredisko')
                ->toggleable(),
            Tables\Columns\TextColumn::make('osob_cislo')
                ->label('osob_cislo')
                ->toggleable(),
            Tables\Columns\TextColumn::make('meno')
                ->label('meno')
                ->toggleable(),
            Tables\Columns\TextColumn::make('suma_cas_skutocny')
                ->label('suma_cas_skutocny')
                ->toggleable(),
            Tables\Columns\TextColumn::make('suma_cas_norma')
                ->label('suma_cas_norma')
                ->toggleable(),
            Tables\Columns\TextColumn::make('plnenie')
                ->label('plnenie')
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
                })->columns(2),
        ];
    }

    public function getExportJobClass(): string
    {
        return ExportSumarReportJob::class;
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
