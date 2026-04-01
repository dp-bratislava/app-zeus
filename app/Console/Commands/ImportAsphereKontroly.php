<?php

namespace App\Console\Commands;

class ImportAsphereKontroly extends AsphereImportBase
{
    protected $signature = 'app:import-asphere-kontroly';
    protected $description = 'Naimportuje kontroly a prace z tabulky combine_table_kontroly';

    protected string $tableName = 'combined_table_kontroly';
    protected string $importType = 'inspection';
}
