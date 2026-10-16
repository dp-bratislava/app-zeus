<?php

namespace App\Reports\Drivers;

use App\Filament\Exports\Reports\KontrolingReportExporter;
use App\Models\Reports\WorkActivityReport;
use Dpb\DatahubSync\Models\Department;
use Dpb\Departments\Services\DepartmentService;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KontrolingReport implements ReportDriver
{
    private ?DepartmentService $departmentService = null;

    public function __construct()
    {
        $this->departmentService = app(DepartmentService::class);
    }

    public function key(): string
    {
        return 'kontroling';
    }

    public function name(): string
    {
        return 'Kontroling';
    }

    public function getQuery(): Builder
    {
        return WorkActivityReport::query()
            ->join('mvw_work_task_subject_snapshots AS wtss', 'wtss.wtf_task_id', '=', 'mvw_work_activity_report_v2.wtf_task_id')
            ->where('wtss.subject_type', 'Vozidlo')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY mvw_work_activity_report_v2.department_code) as id')
            ->selectRaw('mvw_work_activity_report_v2.department_code AS department_code')
            ->selectRaw('wtss.subject_label AS vozidlo')
            ->selectRaw('mvw_work_activity_report_v2.group_sap_code AS group_sap_code')
            ->selectRaw('mvw_work_activity_report_v2.activity_sap_code AS activity_sap_code')
            ->selectRaw('SUM(mvw_work_activity_report_v2.activity_real_duration) AS total_real_duration')
            ->selectRaw("DATE_FORMAT(mvw_work_activity_report_v2.activity_date, '%Y-%m') AS activity_month")
            ->groupByRaw("
                mvw_work_activity_report_v2.department_code,
                wtss.subject_label,
                mvw_work_activity_report_v2.group_sap_code,
                mvw_work_activity_report_v2.activity_sap_code,
                DATE_FORMAT(mvw_work_activity_report_v2.activity_date, '%Y-%m')
            ");
    }

    public function getColumns(): array
    {
        return [
            TextColumn::make('department_code')
                ->label(__('reports/detail-report.table.columns.department_code')),
            TextColumn::make('vozidlo')
                ->label('Vozidlo'),
            TextColumn::make('group_sap_code')
                ->label('SAP skupina'),
            TextColumn::make('activity_sap_code')
                ->label('SAP kód aktivity'),
            TextColumn::make('total_real_duration')
                ->label('Odpracované hodiny')
                ->getStateUsing(function ($record) {
                    $seconds = (int) $record->total_real_duration;

                    if (! $seconds) {
                        return '0:00';
                    }

                    $hours = floor($seconds / 3600);
                    $minutes = floor(($seconds % 3600) / 60);

                    return sprintf('%d:%02d', $hours, $minutes);
                }),
            TextColumn::make('total_real_duration_sap_format')
                ->label('Odpracované hodiny (decimal)')
                ->getStateUsing(fn ($record) => (int) $record->total_real_duration / 3600)
                ->numeric(decimalPlaces: 2),
            TextColumn::make('activity_month')
                ->label('Mesiac'),
        ];
    }

    public function getFilters(): array
    {
        return [
            Filter::make('month')
                ->form([
                    Select::make('months')
                        ->label('Mesiac')
                        ->multiple()
                        ->options(fn () => $this->monthOptions())
                        ->searchable(),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    $months = $data['months'] ?? [];

                    if (! empty($months)) {
                        $placeholders = implode(',', array_fill(0, count($months), '?'));
                        $query->whereRaw(
                            "DATE_FORMAT(mvw_work_activity_report_v2.activity_date, '%Y-%m') IN ($placeholders)",
                            $months
                    );
                    }

                    return $query;
                }),

            SelectFilter::make('department')
                ->label(__('reports/detail-report.table.filters.department'))
                ->options(fn (DepartmentService $departmentSvc) => Department::whereIn('id', $departmentSvc->getAvailableDepartments()->pluck('id'))->pluck('code', 'code'))
                ->multiple()
                ->searchable()
                ->attribute('mvw_work_activity_report_v2.department_code'),
        ];
    }

    private function monthOptions(): array
    {
        $rows = WorkActivityReport::query()
            ->whereIn('department_code', $this->departmentService->getAvailableDepartments()->pluck('code'))
            ->whereNotNull('activity_date')
            ->selectRaw("DATE_FORMAT(activity_date, '%Y-%m') AS ym")
            ->distinct()
            ->orderByDesc('ym')
            ->pluck('ym');

        $options = [];

        foreach ($rows as $ym) {
            $date = Carbon::createFromFormat('Y-m', $ym);
            $options[$ym] = $date->isoFormat('MMMM YYYY');
        }

        return $options;
    }

    public function getSortColumn(): string
    {
        return 'activity_month';
    }

    public function getSortOrder(): string
    {
        return 'desc';
    }

    public function getExporter(): string
    {
        return KontrolingReportExporter::class;
    }

    public function generateExportFilename(): string
    {
        return 'vehicle_'.now()->format('Ymd_His').'.xlsx';
    }

    public function applyQueryModifications(Builder $query): Builder
    {
        return $query->whereIn('mvw_work_activity_report_v2.department_code', $this->departmentService->getAvailableDepartments()->pluck('code'));
    }

    public function lastSyncedAt(): ?string
    {
        return WorkActivityReport::getLastSyncedAt();
    }
}

