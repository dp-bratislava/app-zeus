<?php

namespace Database\Seeders;

use Dpb\Packages\Utils\Models\Currency;
use Dpb\Packages\Utils\Models\MeasurementUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UtilsSeeder extends Seeder
{
    
    public function run(): void
    {
        $prefix = config('pkg-utils.table_prefix');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table($prefix . 'measurement_units')->truncate();
        DB::table($prefix . 'currency')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // measurement units
        $units = [
            ['code' => 'ks', 'title' => 'Kus'],
            ['code' => 'hod', 'title' => 'Hodina'],
            ['code' => 'min', 'title' => 'Minúta'],
            ['code' => 'm', 'title' => 'Meter'],
            ['code' => 'km', 'title' => 'Kilometer'],
        ];

        foreach ($units as $unit) {
            MeasurementUnit::create($unit);
        }

        // currency
        $currencies = [
            ['code' => 'eur', 'title' => 'Euro'],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}