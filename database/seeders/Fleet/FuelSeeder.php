<?php

namespace Database\Seeders\Fleet;

use Dpb\Package\Fleet\Models\FuelConsumptionType;
use Dpb\Package\Fleet\Models\FuelType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelSeeder extends Seeder
{
    
    public function run(): void
    {
        $prefix = config('pkg-fleet.table_prefix');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table($prefix . 'fuel_consumption_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // fuel consumption types
        $consumptionTypes = [
           ['code' => 'fuel_consumption', 'title' => '', 'description' => 'Spotreba mimo mest'],
           ['code' => 'fuel_consumption_city', 'title' => '', 'description' => 'Spotreba v meste'],
           ['code' => 'fuel_consumption_combined', 'title' => '', 'description' => 'Kombinovaná spotreba'],
           ['code' => 'std_fuel_consumption_winter', 'title' => '', 'description' => 'Normovaná spotreba mimo mesto v zime'],
           ['code' => 'std_fuel_consumption_city_winter', 'title' => '', 'description' => 'Normovaná spotreba v meste v zime'],
           ['code' => 'std_fuel_consumption_summer', 'title' => '', 'description' => 'Normovaná spotreba mimo mesto v lete'],
           ['code' => 'std_fuel_consumption_city_summer', 'title' => '', 'description' => 'Normovaná spotreba v meste v lete'],
        ];

        foreach ($consumptionTypes as $consumptionType) {
            FuelConsumptionType::create($consumptionType);
        }
    }
}