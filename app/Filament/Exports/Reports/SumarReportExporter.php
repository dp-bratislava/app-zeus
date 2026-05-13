<?php

namespace App\Filament\Exports\Reports;

use App\Reports\Exports\BaseReportExporter;
use Illuminate\Support\Facades\DB;

class SumarReportExporter extends BaseReportExporter
{
    protected string $baseUrl = 'http://host.docker.internal:8111';

    protected function columns(): array
    {
        return [
            ['key' => 'stredisko', 'label' => 'Stredisko'],
            ['key' => 'osob_cislo', 'label' => 'Osobné číslo'],
            ['key' => 'meno', 'label' => 'Meno'],
            ['key' => 'suma_cas_skutocny', 'label' => 'Suma čas skutočný', 'type' => 'duration'],
            ['key' => 'suma_cas_norma', 'label' => 'Suma čas norma', 'type' => 'duration'],
            ['key' => 'plnenie', 'label' => 'Plnenie (%)'],
        ];
    }

        protected function query(array $filters)
    {
        // We use DB::table to match the driver's logic but ensure it's compatible with raw export
        return DB::table('dpb_worktimefund_model_activityrecord')
            ->leftJoin('dpb_worktimefund_model_worktime as wt', 'wt.id', '=', 'dpb_worktimefund_model_activityrecord.parent_id')
            ->leftJoin('datahub_employee_contracts as c', 'c.pid', '=', 'wt.personal_id')
            ->leftJoin('datahub_departments as d', 'd.id', '=', 'c.datahub_department_id')
            ->select([
                'd.code as stredisko',
                // Note: ROW_NUMBER might be tricky for some remote exporters if not supported by the DB engine, 
                // but included here to match your driver logic.
                DB::raw("ROW_NUMBER() OVER (ORDER BY c.pid) as id"),
                DB::raw("TRIM(LEADING '0' FROM c.pid) as osob_cislo"),
                DB::raw("CONCAT(wt.last_name, ' ', wt.first_name) AS meno"),
                DB::raw("SUM(dpb_worktimefund_model_activityrecord.real_duration) AS suma_cas_skutocny"),
                DB::raw("SUM(dpb_worktimefund_model_activityrecord.expected_duration) AS suma_cas_norma"),
                DB::raw("ROUND(100 * SUM(dpb_worktimefund_model_activityrecord.expected_duration) / SUM(dpb_worktimefund_model_activityrecord.real_duration), 0) AS plnenie"),
            ])
            ->where('dpb_worktimefund_model_activityrecord.type', 'O')
            
            // Apply Filters (matching the driver's date logic)
            ->when(data_get($filters, 'date.date_from'), function ($q, $v) {
                $q->whereDate('dpb_worktimefund_model_activityrecord.date', '>=', $v);
            })
            ->when(data_get($filters, 'date.date_to'), function ($q, $v) {
                $q->whereDate('dpb_worktimefund_model_activityrecord.date', '<=', $v);
            })
            // Department filter (if passed from Filament)
            ->when(data_get($filters, 'department.values'), function ($q, $v) {
                $q->whereIn('d.code', $v);
            })
            
            ->groupBy('c.pid', 'wt.last_name', 'wt.first_name', 'd.code');
    }

    public function getExporterName(): string
    {
        return 'XLSWriter-Remote';
    }
}

