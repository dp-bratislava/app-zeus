<?php

namespace Database\Seeders;

use Database\Seeders\WTF\EnumSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,

            // app specific seeders
            EnumSeeder::class,
        ]);
    }
}