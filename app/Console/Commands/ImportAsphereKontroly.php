<?php

namespace App\Console\Commands;

class ImportAsphereKontroly extends AsphereImportBase
{
    protected $signature = 'app:import-asphere-kontroly';
    protected $description = 'Import Asphere kontroly with enhanced batch tracking and error handling';

    protected string $tableName = 'combined_table_kontroly';
    protected bool $usesInspection = true;
}
