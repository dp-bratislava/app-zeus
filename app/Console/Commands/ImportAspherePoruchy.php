<?php

namespace App\Console\Commands;

class ImportAspherePoruchy extends AsphereImportBase
{
    protected $signature = 'app:import-asphere-poruchy';
    protected $description = 'Import Asphere data with enhanced batch tracking and error handling';

    protected string $tableName = 'combined_table_poruchy';
    protected bool $usesTitleDescription = true;
}
