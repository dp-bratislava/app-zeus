<?php

namespace App\Console\Commands;

class ImportAspherePoruchy extends AsphereImportBase
{
    protected $signature = 'app:import-asphere-poruchy';
    protected $description = 'Naimportuje poruchy a prace z tabulky combined_table_poruchy';

    protected string $tableName = 'combined_table_poruchy';
    protected string $importType = 'malfunction';
}
