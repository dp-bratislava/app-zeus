<?php

namespace App\Filament\Exports\Reports;

use App\Reports\Exports\BaseReportExporter;
use Dpb\Departments\Services\DepartmentService;
use Illuminate\Support\Facades\DB;

class KontrolingDetailReportExporter extends BaseReportExporter
{
    private ?DepartmentService $departmentService = null;
    private array $filters = [];

    public function __construct()
    {
        parent::__construct();
        $this->departmentService = app(DepartmentService::class);
    }

    protected const DYNAMIC_COLUMN_PREFIX = 'dc_';

    protected function dynamicColumns(): array
    {
        $departmentCodes = data_get($this->filters, 'department.values');
        if(empty($departmentCodes)) {
            $departmentCodes = $this->departmentService->getAvailableDepartments()->pluck('code')->toArray();
        }

        // v3 carries subject_type/subject_label inline, so no extra join is needed.
        $subjectTypes = DB::table('mvw_work_activity_report_v3 AS ar')
            ->whereIn('ar.department_code', $departmentCodes)
            ->whereNotNull('ar.subject_type')
            ->distinct()
            ->pluck('ar.subject_type')
            ->toArray();

        $columns = [];

        foreach ($subjectTypes as $key => $type) {
            $columns[] = [
                'key' => self::DYNAMIC_COLUMN_PREFIX . $key,
                'label' => $type,
                'static' => false,
                'definition' => DB::raw("
                    MAX(CASE 
                        WHEN ar.subject_type = '$type' 
                        THEN ar.subject_label 
                    END) AS " . self::DYNAMIC_COLUMN_PREFIX . $key
                )
            ];
        }

        return $columns;
    }

    protected function columns(): array
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
            ['key' => 'group_sap_code', 'label' => 'SAP Kód'],
            ['key' => 'activity_sap_code', 'label' => 'Kód aktivity'],
            ['key' => 'task_item_assigned_to_label', 'label' => 'Prevádzka podzákazky'],
            ['key' => 'task_item_author_lastname', 'label' => 'Podzákazku vytvoril'],
            ['key' => 'wtf_task_created_at', 'label' => 'Čas priradenia práce'],
            ['key' => 'activity_date', 'label' => 'Dátum výkonu práce', 'type' => 'date'],
            ['key' => 'personal_id', 'label' => 'Osobné číslo'],
            ['key' => 'last_name', 'label' => 'Priezvisko'],
            ['key' => 'first_name', 'label' => 'Meno'],
            ['key' => 'activity_title', 'label' => 'Názov úkonu', 'width' => 35],
            ['key' => 'activity_expected_duration', 'label' => 'Norma', 'type' => 'duration'],
            ['key' => 'activity_real_duration', 'label' => 'Výkon', 'type' => 'duration'],
            ['key' => 'total_real_duration_sap_format', 'label' => 'Výkon (decimal)'],
            ...$this->dynamicColumns()
        ];
    }

    protected function query(array $filters)
    {
        $this->filters = $filters;
        $departmentCodes = data_get($filters, 'department.values');
        $dynamicColumns = array_column(
            array_filter($this->columns(), fn($c) => (isset($c['static']) && $c['static'] == false)),
            'definition'
        );

        $result =  DB::table('mvw_work_activity_report_v3 AS ar')
            ->select([
                'ar.id',
                DB::raw("REPLACE(FORMAT(ar.activity_real_duration / 3600, 2), '.', ',') AS total_real_duration_sap_format"),
                'department_code',
                'task_created_at',
                'activity_is_fulfilled_label',
                'task_date',
                'task_group_title',
                'task_assigned_to_label',
                'task_author_lastname',
                'task_item_group_title',
                'group_sap_code',
                'activity_sap_code',
                'task_item_assigned_to_label',
                'task_item_author_lastname',
                'wtf_task_created_at',
                'activity_date',
                'personal_id',
                'last_name',
                'first_name',
                'activity_title',
                'activity_expected_duration',
                'activity_real_duration',
                ...$dynamicColumns,
            ])
            ->groupBy([
                'ar.id',
            ])
            ->when(data_get($filters, 'date_range.date_from'), fn($q, $v) => $q->whereDate('activity_date', '>=', $v))
            ->when(data_get($filters, 'date_range.date_to'), fn($q, $v) => $q->whereDate('activity_date', '<=', $v))
            ->when(!empty($departmentCodes), function ($q) use ($departmentCodes) {
                $q->whereIn('department_code', $departmentCodes);
            })
            // Always apply available departments filter (same as applyQueryModifications)
            ->whereIn('department_code', $this->departmentService->getAvailableDepartments()->pluck('code'));

            return $result;
    }

    public function getExporterName(): string
    {
        return 'XLSWriter-Remote';
    }
}
