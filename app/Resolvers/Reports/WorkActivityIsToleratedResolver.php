<?php

namespace App\Resolvers\Reports;

use Illuminate\Support\Facades\DB;

class WorkActivityIsToleratedResolver
{
    public function batchResolve(array $groupedIds): array
    {
        $result = [];

        // absences
        // @TODO 
        if (!empty($groupedIds['A'])) {
            foreach ($groupedIds['A'] as $row) {
                $result['A'][$row->id] = [
                    'is_tolerated' => null,
                ];
            }
            // $rows = DB::table('dpb_worktimefund_model_absence')
            //     ->whereIn('id', $groupedIds['A'])
            //     ->get()
            //     ->keyBy('id');

            // foreach ($rows as $row) {
            //     $result['A'][$row->id] = [
            //         'is_tolerated' => $row->is_tolerated,
            //         'type' => 'absence',
            //     ];
            // }
        }

        // operations / work
        if (!empty($groupedIds['O'])) {
            foreach ($groupedIds['O'] as $row) {
                $result['O'][$row->id] = [
                    'is_tolerated' => null,
                ];
            }
        }

        return $result;
    }
}
