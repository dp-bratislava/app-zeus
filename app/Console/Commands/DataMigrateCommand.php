<?php

namespace App\Console\Commands;

use App\DataMigrations\BreakActivityMigration;
use App\DataMigrations\VehicleCleaningBMigration;
use App\DataMigrations\OperationCategoryDepartmentSync;
use App\DataMigrations\ScalableOperationMigration;
use App\DataMigrations\TaskBatch\BatchContextMigration;
use App\DataMigrations\TaskBatch\DailyMaintenanceMigration;
use App\DataMigrations\TaskBatch\ReconcileOldDataMigration;
use App\DataMigrations\TaskBatch\VehicleCleaningMigration;
use App\DataMigrations\WtfFinanceMigration;
use Illuminate\Console\Command;

class DataMigrateCommand extends Command
{
    protected $signature = 'data:migrate {profile}';

    public function handle(): int
    {
        match ($this->argument('profile')) {
            // task batch
            'tb.reconcile-old-data' => app(ReconcileOldDataMigration::class)->run(),
            'tb.batch-context' => app(BatchContextMigration::class)->run(),
            'tb.vehicle-cleaning' => app(VehicleCleaningMigration::class)->run(),
            'tb.daily-maintenance' => app(DailyMaintenanceMigration::class)->run(),
            
            'vehicle-cleaning-b' => app(VehicleCleaningBMigration::class)->run(),
            'scalable-operation' => app(ScalableOperationMigration::class)->run(),
            'break-activity' => app(BreakActivityMigration::class)->run(),
            'operation-category-department-sync' => app(OperationCategoryDepartmentSync::class)->run(),
            // 'inspection-rules'   => app(InspectionRulesMigration::class)->run(),
            // default => throw new InvalidArgumentException(...),
            default => null
        };
        
        return self::SUCCESS;    
    }

}
