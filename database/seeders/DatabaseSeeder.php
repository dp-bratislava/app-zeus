<?php

namespace Database\Seeders;

use Database\Seeders\WTF\EnumSeeder as WTFEnumSeeder;
use Database\Seeders\Fleet\EnumSeeder as FleetEnumSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,

            // app specific seeders
            WTFEnumSeeder::class,
            FleetEnumSeeder::class,
        ]);
    }
}