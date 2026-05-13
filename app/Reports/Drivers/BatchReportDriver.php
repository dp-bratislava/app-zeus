<?php

namespace App\Reports\Drivers;

use App\Jobs\Reports\ExportBatchReportJob;
use App\Models\Snapshots\ReportSyncState;
use App\Models\Snapshots\WorkTaskSubject;
use Carbon\CarbonInterval;
use Dpb\Departments\Services\DepartmentService;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class BatchReportDriver implements ReportDriver
{
    private ?DepartmentService $departmentService = null;

    public function __construct()
    {
        $this->departmentService = app(DepartmentService::class);
    }

    public function key(): string
    {
        return 'batch';
    }

    public function name(): string
    {
        return 'no je to loadnute';
    }

    public function icon(): string
    {
        return 'heroicon-o-briefcase';
    }

    public function getQuery(): Builder
    {
        return DB::table('tmp_asphere_import_batchable_batch_records'); 
    }

    public function getColumns(): array
    {
        $latestSync = 'never';


        return[
            Tables\Columns\TextColumn::make('record_id')
                ->label('konina')
                ->toggleable()];
        
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getExportJobClass(): string
    {
        return ExportBatchReportJob::class;
    }

    public function getExportTemplates(): array
    {
        return [
            'default' => 'Default Template',
        ];
    }

    public function applyQueryModifications(Builder $query): Builder
    {
        return $query;
    }

    public function getExportColumns(): array
    {
        return [
            ['key' => 'record_id', 'label' => 'konina jak zito'],

        ];
    }

    public function buildExportFilters(array $filters): array
    {
        return $filters;
    }
}
