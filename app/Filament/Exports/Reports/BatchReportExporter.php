<?php

namespace App\Filament\Exports\Reports;

use App\Reports\Exports\BaseReportExporter;
use Illuminate\Support\Facades\DB;

class BatchReportExporter extends BaseReportExporter
{
    protected string $baseUrl = 'http://host.docker.internal:8111';

    protected function columns(): array
    {
        return [
            ['key' => 'record_id', 'label' => 'konina jak pec'],

        ];
    }

    protected function query(array $filters)
    {
        $departmentCodes = data_get($filters, 'department.values');
        return DB::table('tmp_asphere_import_batchable_batch_records')
           ->select('record_id');
    }

    public function getExporterName(): string
    {
        return 'XLSWriter-Remote';
    }
}

