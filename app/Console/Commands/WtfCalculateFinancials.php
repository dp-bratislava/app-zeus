<?php

namespace App\Console\Commands;

use App\DataMigrations\BreakActivityMigration;
use App\DataMigrations\OperationCategoryDepartmentSync;
use App\DataMigrations\ScalableOperationMigration;
use App\DataMigrations\VehicleCleaningBMigration;
use App\DataMigrations\WtfFinanceMigration;
use Illuminate\Console\Command;

class WtfCalculateFinancials extends Command
{
    protected $signature = 'wtf-fin:calculate {taskId}';

    public function handle()
    {
        $taskId = $this->argument('taskId');


        
        match ($this->argument('profile')) {
            'vehicle-cleaning-b' => app(VehicleCleaningBMigration::class)->run(),
            'scalable-operation' => app(ScalableOperationMigration::class)->run(),
            'break-activity' => app(BreakActivityMigration::class)->run(),
            'operation-category-department-sync' => app(OperationCategoryDepartmentSync::class)->run(),
            // 'inspection-rules'   => app(InspectionRulesMigration::class)->run(),
            // default => throw new InvalidArgumentException(...),
            'wtf-finance' => app(WtfFinanceMigration::class)->run(),
            default => null
        };
    }

}
