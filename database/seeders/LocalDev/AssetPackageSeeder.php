<?php

// run assetslot seeder and asset states seeder

namespace Database\Seeders\LocalDev;

use Illuminate\Database\Seeder;

/**
 * Run: ./vendor/bin/sail artisan db:seed --class="Database\Seeders\LocalDev\AssetPackageSeeder"
 */
class AssetPackageSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AssetSlotSeeder::class);
    }
}
