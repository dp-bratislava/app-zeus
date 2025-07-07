<?php

namespace Database\Seeders\Fleet;

use App\Models\Fleet\ServiceGroup;
use App\Models\Fleet\TransportGroup;
use App\Models\Fleet\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnumSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('dpb_fleet_vehicle_types')->truncate();
        DB::table('dpb_fleet_transport_groups')->truncate();
        DB::table('dpb_fleet_service_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // vehicle types
        $vehicleTypes = [
            ['code' => 'A', 'title' => 'Autobus'],
            ['code' => 'E', 'title' => 'Električka'],
            ['code' => 'T', 'title' => 'Trolejbus'],
        ];

        foreach ($vehicleTypes as $vehicleType) {
            VehicleType::create($vehicleType);
        }

        // // transport groups
        // $transportGroups = [
        //     ['short_title' => '1.DPA', 'title' => 'gg'],
        //     ['short_title' => '1.DPA', 'title' => 'gg'],
        //     ['short_title' => '1.DPA', 'title' => 'gg'],
        // ];

        // foreach ($transportGroups as $transportGroup) {
        //     TransportGroup::create($transportGroup);
        // }

        // // service groups
        // $serviceGroups = [
        //     ['short_title' => '1.DPA', 'title' => 'gg'],
        //     ['short_title' => '1.DPA', 'title' => 'gg'],
        //     ['short_title' => '1.DPA', 'title' => 'gg'],
        // ];

        // foreach ($serviceGroups as $serviceGroup) {
        //     ServiceGroup::create($serviceGroup);
        // }
    }
}
