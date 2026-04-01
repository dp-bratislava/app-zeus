<?php

namespace App\Console\Commands;

class ImportDailyInspections extends AsphereImportBase
{
    protected $signature = 'import:daily-inspection';
    protected $description = 'Import daily inspection data with task and title/description fields';
    protected string $creationDateColumn = 'date';
    protected string $workDateFormat = 'd.m.Y';
    protected string $creationDateFormat = 'd.m.Y';
    protected string $workDateColumn = 'date';
    protected string $activityRecordRealTimeColumn = 'duration';
    protected string $noteColumn = 'note';
    protected string $tableName = 'DO_elektrika';
    protected string $importType = 'daily-maintenance';
}
