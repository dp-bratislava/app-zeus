<?php

namespace App\Resolvers\Reports;

use Dpb\WorkTimeFund\Models\Maintainables\Table;
use Dpb\WorkTimeFund\Models\Maintainables\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkTaskSubjectResolver
{

    public function batchResolve($taskIds): ?array
    {
        $result = [];

        // vehicles
        $result = $this->resolveVehicles($taskIds);

        // bus stop tables
        // $result = $this->resolveVehicles($taskIds);

        // custom
        // $result = $this->resolveCustom($taskIds);

        // dd($result);
        return $result;
    }

    protected function resolveCustom(array $taskIds): ?array
    {
        return null;
    }

protected function resolveVehicles(array $taskIds): array
{
    $tasks = DB::table('dpb_worktimefund_model_task')
        ->whereIn('id', $taskIds)
        ->where('maintainable_type', 'Dpb\WorkTimeFund\Models\Maintainables\Vehicle')
        ->select(['id', 'maintainable_id'])
        ->get();

    if ($tasks->isEmpty()) {
        return [];
    }

    $vehicleIds = $tasks->pluck('maintainable_id');

    $vehicles = DB::table('fleet_vehicles as v')
        ->leftJoin('fleet_vehicle_code_history as vch', 'vch.vehicle_id', '=', 'v.id')
        ->leftJoin('fleet_vehicle_codes as vc', 'vc.id', '=', 'vch.vehicle_code_id')
        ->leftJoin('fleet_licence_plate_history as lph', 'lph.vehicle_id', '=', 'v.id')
        ->leftJoin('fleet_licence_plates as lp', 'lp.id', '=', 'lph.licence_plate_id')
        ->whereIn('v.id', $vehicleIds)
        ->select([
            'v.id',
            DB::raw('COALESCE(vc.code, lp.code) as label')
        ])
        ->get()
        ->keyBy('id');

    $result = [];

    foreach ($tasks as $task) {
        $vehicle = $vehicles[$task->maintainable_id] ?? null;

        $result[$task->id] = [
            'label' => $vehicle->label ?? null,
            'type' => 'vehicle',
        ];
    }

    return $result;
}

    protected function resolveVehicles1(array $taskIds): ?array
    {
        $tasksWithVehicles = DB::table('dpb_worktimefund_model_task')
            ->whereIn('id', $taskIds)
            ->where('maintainable_type', '=', 'Dpb\WorkTimeFund\Models\Maintainables\Vehicle')
            ->distinct()
            ->select([
                'id',
                'maintainable_id'
            ])
            ->get()
            ->keyBy('id');

        if (!empty($tasksWithVehicles)) {
            // dd($tasksWithVehicles->pluck('maintainable_id')->toArray());
            $vehicleIds = $tasksWithVehicles->pluck('maintainable_id')->toArray();

            $latestCode = DB::table('fleet_vehicle_code_history')
                ->select('vehicle_id', DB::raw('MAX(date_from) as max_date'))
                ->whereIn('vehicle_id', $vehicleIds)
                ->groupBy('vehicle_id');

            $latestPlate = DB::table('fleet_licence_plate_history')
                ->select('vehicle_id', DB::raw('MAX(date_from) as max_date'))
                ->whereIn('vehicle_id', $vehicleIds)
                ->groupBy('vehicle_id');

            $vehicles = DB::table('fleet_vehicles as v')
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
                ->whereIn('v.id', $vehicleIds)
                ->select([
                    'v.id',
                    DB::raw('COALESCE(vc.code, flp.code) as label')
                ])
                ->get()
                ->keyBy('id')
                ->toArray();
            $result = [];
            foreach ($tasksWithVehicles as $row) {
                $result[$row->id] = [
                    'label' => $vehicles[$row->maintainable_id]->label ?? null,
                    'type' => 'vehicle',
                ];
            }
        }

        return $result;
    }

    protected function resolveTables(array $ids): ?array
    {
        $latestLocation = DB::table('dvc_device_location_history')
            ->select('device_id', DB::raw('MAX(date_from) as max_date'))
            ->whereIn('device_id', $ids)
            ->groupBy('device_id');

        $rows = DB::table('dvc_devices as d')
            // locations
            ->leftJoinSub(
                $latestLocation,
                'll',
                fn($j) =>
                $j->on('d.id', '=', 'll.device_id')
            )
            ->leftJoin('dvc_device_location_history as dlh', function ($join) {
                $join->on('dlh.device_id', '=', 'll.device_id')
                    ->on('dlh.date_from', '=', 'll.max_date');
            })
            ->leftJoin('dvc_device_locations as dl', 'dl.id', '=', 'dlh.location_id')
            ->whereIn('d.id', $ids)
            ->select([
                'd.id',
                'dl.title as label'
            ])
            ->get()
            ->keyBy('id')
            ->toArray();

        return $rows;
    }


    private function resolveCustom1($model): ?array
    {
        return null;
        // if (empty($model->attributeOptions)) {
        //     return null;
        // }

        // // Task
        // $types = [];
        // $labels = [];
        // foreach ($model->attributeOptions as $attribute) {
        //     $types[] = $attribute->type->title;
        //     $labels[] = $attribute->label;
        // }

        // return [
        //     'type' => join(', ', $types),
        //     'label' => join(', ', $labels),
        // ];
        /*
        $attribute = $model->attributes->first();
        // dd(
        //     $attribute->label,
        //     // $attribute->type->title
        // );
        return [
            'subject_type' => $attribute->type->title,
            'subject_id' => $attribute->id,
            'label' => $attribute->label,
            'code' => null,
            'meta' => json_encode([], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];
        */
    }
}
