<?php

namespace App\Resolvers\Snapshots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaskRequestedForResolver
{
    public function preload(array $groupedIds): array
    {
        $result = [];

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
