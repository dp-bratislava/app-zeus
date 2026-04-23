<?php

namespace App\Resolvers\Snapshots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaskItemAssignedToResolver
{
    public function preload(array $groupedIds): array
    {
        $result = [];

        // maintenance groups
        if (!empty($groupedIds['maintenance-group'])) {
            $rows = DB::table('fleet_maintenance_groups')
                ->whereIn('id', $groupedIds['maintenance-group'])
                ->get()
                ->keyBy('id');

            foreach ($rows as $row) {
                $result['maintenance-group'][$row->id] = [
                    'label' => $row->code,
                    'type' => 'maintenance-group',
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
