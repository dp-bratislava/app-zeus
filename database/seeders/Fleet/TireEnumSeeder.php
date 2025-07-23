<?php

namespace Database\Seeders\Fleet;

use App\Models\Fleet\Tire\ConstructionType;
use App\Models\Fleet\Tire\Season;
use App\Models\Fleet\Tire\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TireEnumSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('dpb_fleet_tire_statuses')->truncate();
        DB::table('dpb_fleet_tire_seasons')->truncate();
        DB::table('dpb_fleet_tire_construction_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // tire seasons
        $tireSeasons = [
            ['code' => 'C', 'title' => 'Celoročná'],
            ['code' => 'L', 'title' => 'Letná'],
            ['code' => 'Z', 'title' => 'Zimná'],
        ];

        foreach ($tireSeasons as $tireSeason) {
            Season::create($tireSeason);
        }

        // tire construction types 
        $constructionTypes = [
            ['code' => 'R', 'title' => 'Radial'],
            ['code' => 'D', 'title' => 'Diagonal'],
            ['code' => 'B', 'title' => 'BiasBelted'],
        ];

        foreach ($constructionTypes as $constructionType) {
            ConstructionType::create($constructionType);
        }

        // tire statuses
        $tireStatuses = [
            ['code' => '1', 'title' => 'Sklad'],
            ['code' => '2', 'title' => 'Vozidlo'],
            ['code' => '3', 'title' => 'Oprava'],
            ['code' => '4', 'title' => 'Vyradené opraviteľne'],
            ['code' => '5', 'title' => 'Vyradené neopraviteľne'],
            ['code' => '6', 'title' => 'Prevádzka'],
        ];

        foreach ($tireStatuses as $tireStatus) {
            Status::create($tireStatus);
        }
    }
}
