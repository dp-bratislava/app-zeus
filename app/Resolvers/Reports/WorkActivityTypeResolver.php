<?php

namespace App\Resolvers\Reports;

class WorkActivityTypeResolver
{
    public function batchResolve(array $groupedIds): array
    {
        $result = [];

        // absences
        if (!empty($groupedIds['A'])) {
            foreach ($groupedIds['A'] as $id) {
                $result['A'][$id] = [
                    'type' => 'absence',
                ];
            }
        }

        // operations / work
        if (!empty($groupedIds['O'])) {
            foreach ($groupedIds['O'] as $id) {
                $result['O'][$id] = [
                    'type' => 'work',
                ];
            }
        }

        return $result;
    }
}
