<?php

namespace App\Console\Commands;

use App\DataMigrations\BreakActivityMigration;
use App\DataMigrations\ScalableOperationMigration;
use App\DataMigrations\VehicleCleaningBMigration;
use Illuminate\Console\Command;

class DataMigrateCommand extends Command
{
    protected $signature = 'app:data-migrate {profile}';

    public function handle(): int
    {
        match ($this->argument('profile')) {
            'vehicle-cleaning-b' => app(VehicleCleaningBMigration::class)->run(),
            'scalable-operation' => app(ScalableOperationMigration::class)->run(),
            'break-activity' => app(BreakActivityMigration::class)->run(),
            // 'inspection-rules'   => app(InspectionRulesMigration::class)->run(),
            // default => throw new InvalidArgumentException(...),
            default => null
        };
        
        return self::SUCCESS;    
    }

}
