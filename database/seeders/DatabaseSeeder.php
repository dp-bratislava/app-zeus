<?php

namespace Database\Seeders;

use Database\Seeders\WTF\EnumSeeder as WTFEnumSeeder;
use Database\Seeders\Fleet\EnumSeeder as FleetEnumSeeder;
use Database\Seeders\Fleet\InspectionEnumSeeder;
use Database\Seeders\Fleet\TireEnumSeeder;
use Database\Seeders\TS\IssueEnumSeeder;
use Database\Seeders\TS\TaskEnumSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,

            // app specific seeders
            // WTFEnumSeeder::class,
            // fleet
            // FleetEnumSeeder::class,
            // TireEnumSeeder::class,
            // InspectionEnumSeeder::class,
            // TS
            // TaskEnumSeeder::class,
            // IssueEnumSeeder::class,
        ]);
    }
}