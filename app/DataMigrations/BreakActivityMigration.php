<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\WorkTimeFund\Models\BreakActivity;
use Illuminate\Support\Facades\Artisan;

/**
 * Set all operations as scalable for department 9486
 */
class BreakActivityMigration implements DataMigration
{
    public function run(): void
    {
        // create break activity
        BreakActivity::insertOrIgnore([
            'id' => 1,
            'title' => 'Sprcha',
            'duration' => 900,
            'is_tolerated' => 1
        ]);

        // build mapping between employee contract and break
        // based on config rules
        Artisan::call('wtf:sync-break-mappings');

        // generate breaks for specified departmetns and time period
        $departments = [7213, 7223, 7233];
        $dateFrom = '2026-07-01';
        $dateTo = '2026-07-31';

        foreach ($departments as $department) {
            Artisan::call('wtf:generate-break-activities', [
                'department-code' => $department,
                'date-from' => $dateFrom,
                'date-to' => $dateTo
            ]);
        }
    }
}
