<?php

namespace App\Console\Commands;

use App\DataMigrations\VehicleCleaningBMigration;
use Illuminate\Console\Command;

class DataMigrateCommand extends Command
{
    protected $signature = 'app:data-migrate {profile}';

    public function handle(): int
    {
        match ($this->argument('profile')) {
            'vehicle-cleaning-b' => app(VehicleCleaningBMigration::class)->run(),
            // 'inspection-rules'   => app(InspectionRulesMigration::class)->run(),
            // default => throw new InvalidArgumentException(...),
            default => null
        };
        
        return self::SUCCESS;    
    }

}
