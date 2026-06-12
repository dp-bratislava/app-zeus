<?php

namespace App\DataMigrations\Contracts;

interface DataMigration
{
    public function run(): void;
}