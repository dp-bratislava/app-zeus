<?php

namespace App\Resolvers\Snapshots;

use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaskSubjectResolver
{
    public function preload(array $groupedIds): array
    {
        $result = [];
        // vehicles
        if (!empty($groupedIds['vehicle'])) {
            $latestCode = DB::table('fleet_vehicle_code_history')
                ->select('vehicle_id', DB::raw('MAX(date_from) as max_date'))
                ->whereIn('vehicle_id', $groupedIds['vehicle'])
                ->groupBy('vehicle_id');

            $latestPlate = DB::table('fleet_licence_plate_history')
                ->select('vehicle_id', DB::raw('MAX(date_from) as max_date'))
                ->whereIn('vehicle_id', $groupedIds['vehicle'])
                ->groupBy('vehicle_id');

            $rows = DB::table('fleet_vehicles as v')
                // codes
                ->leftJoinSub(
                    $latestCode,
                    'lc',
                    fn($j) =>
                    $j->on('v.id', '=', 'lc.vehicle_id')
                )
                ->leftJoin('fleet_vehicle_code_history as vch', function ($join) {
                    $join->on('vch.vehicle_id', '=', 'lc.vehicle_id')
                        ->on('vch.date_from', '=', 'lc.max_date');
                })
                ->leftJoin('fleet_vehicle_codes as vc', 'vc.id', '=', 'vch.vehicle_code_id')
                
                // licence plates                
                ->leftJoinSub(
                    $latestPlate,
                    'lp',
                    fn($j) =>
                    $j->on('v.id', '=', 'lp.vehicle_id')
                )
                ->leftJoin('fleet_licence_plate_history as lph', function ($join) {
                    $join->on('lph.vehicle_id', '=', 'lp.vehicle_id')
                        ->on('lph.date_from', '=', 'lp.max_date');
                })
                ->leftJoin('fleet_licence_plates as flp', 'flp.id', '=', 'lph.licence_plate_id')
                ->whereIn('v.id', $groupedIds['vehicle'])
                ->select([
                    'v.id',
                    DB::raw('COALESCE(vc.code, flp.code) as label')
                ])

                ->get()
                ->keyBy('id');

            foreach ($rows as $row) {
                $result['vehicle'][$row->id] = [
                    'label' => $row?->label,
                    'type' => 'vehicle',
                ];
            }
        }

        // departments
        if (!empty($groupedIds['department'])) {
            $rows = DB::table('datahub_departments')
                ->whereIn('id', $groupedIds['department'])
                ->get()
                ->keyBy('id');

            foreach ($rows as $row) {
                $result['department'][$row->id] = [
                    'label' => $row->code,
                    'type' => 'department',
                ];
            }
        }

        return $result;
    }
}
