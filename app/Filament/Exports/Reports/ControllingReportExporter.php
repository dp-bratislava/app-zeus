<?php

namespace App\Filament\Exports\Reports;

use App\Reports\Exports\BaseReportExporter;
use Dpb\Departments\Services\DepartmentService;
use Illuminate\Support\Facades\DB;

class ControllingReportExporter extends BaseReportExporter
{
    private ?DepartmentService $departmentService = null;

    public function __construct()
    {
        parent::__construct();
        $this->departmentService = app(DepartmentService::class);
    }

    protected function columns(): array
    {
        return [
            ['key' => 'department_code', 'label' => 'Stredisko'],
            ['key' => 'vozidlo', 'label' => 'Vozidlo'],
            ['key' => 'subject_department', 'label' => 'Nákladové stredisko vozu'],
            ['key' => 'group_sap_code', 'label' => 'SAP skupina'],
            ['key' => 'activity_sap_code', 'label' => 'SAP kód aktivity'],
            ['key' => 'total_real_duration', 'label' => 'Odpracované hodiny', 'type' => 'duration'],
            ['key' => 'total_real_duration_sap_format', 'label' => 'Odpracované hodiny (decimal)'],
            ['key' => 'activity_month', 'label' => 'Mesiac'],
        ];
    }

    protected function query(array $filters)
    {
        $departmentCodes = data_get($filters, 'department.values');
        $months = data_get($filters, 'month.months', []);

        return DB::table('mvw_work_activity_report_v3 AS ar')
            ->where('ar.subject_type', 'Vozidlo')
            ->select([
                'ar.department_code as department_code',
                'ar.subject_label as vozidlo',
                'ar.subject_department as subject_department',
                'ar.group_sap_code as group_sap_code',
                'ar.activity_sap_code as activity_sap_code',
                DB::raw('SUM(ar.activity_real_duration) AS total_real_duration'),
                DB::raw("REPLACE(FORMAT(SUM(ar.activity_real_duration) / 3600, 2), '.', ',') AS total_real_duration_sap_format"),
                DB::raw("DATE_FORMAT(ar.activity_date, '%Y-%m') AS activity_month"),
            ])
            ->when(! empty($months), function ($q) use ($months) {
                $placeholders = implode(',', array_fill(0, count($months), '?'));
                $q->whereRaw(
                    "DATE_FORMAT(ar.activity_date, '%Y-%m') IN ($placeholders)",
                    array_values($months)
                );
            })
            ->when(! empty($departmentCodes), function ($q) use ($departmentCodes) {
                $q->whereIn('ar.department_code', $departmentCodes);
            })
            // Always apply available departments filter (same as applyQueryModifications)
            ->whereIn('ar.department_code', $this->departmentService->getAvailableDepartments()->pluck('code'))
            ->groupByRaw("
                ar.department_code,
                ar.subject_label,
                ar.subject_department,
                ar.group_sap_code,
                ar.activity_sap_code,
                DATE_FORMAT(ar.activity_date, '%Y-%m')
            ");
    }

    public function getExporterName(): string
    {
        return 'XLSWriter-Remote';
    }
}

