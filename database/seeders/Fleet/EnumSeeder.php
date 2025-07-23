<?php

namespace Database\Seeders\Fleet;

use App\Models\Fleet\ServiceGroup;
use App\Models\Fleet\TransportGroup;
use App\Models\Fleet\VehicleStatus;
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

        // vehicle statuses
        $vehicleStatuses = [
            ['code' => 'dispatched', 'title' => 'Vypravené'],
            ['code' => 'reserve', 'title' => 'Rezerva'],
            ['code' => 'broken', 'title' => 'Porucha'],
        ];

        foreach ($vehicleStatuses as $vehicleStatus) {
            VehicleStatus::create($vehicleStatus);
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

        // service groups
        $serviceGroups = [
            ['short_title' => 'PEK', 'title' => 'Prevádzka električiek Krasňany'],
            ['short_title' => 'PEJ', 'title' => 'Prevádzka električiek Jurajov dvor'],
            ['short_title' => 'PTH', 'title' => 'Prevádzka trolejbusov Hroboňova'],
            ['short_title' => 'PTT', 'title' => 'Prevádzka trolejbusov Trnávka'],
        ];

        foreach ($serviceGroups as $serviceGroup) {
            ServiceGroup::create($serviceGroup);
        }
    }
}
