<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers;

use Dpb\Package\Tasks\Models\TaskItemGroup;
use Illuminate\Support\Facades\DB;

final class TaskBatchInfolistLabelResolver
{
    /**
     * @param array<string, array<int|string>> $subjects
     * @return array<string, array{label: string, description: string}>
     */
    public function resolveSubjectLabels(array $subjects): array
    {
        $labels = [];

        foreach ($subjects as $type => $ids) {
            $ids = collect($ids)
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($ids->isEmpty()) {
                continue;
            }

            // Add a resolver for each supported subject type here.
            switch ($type) {
                case 'vehicle':
                    $labels += DB::table('mvw_fleet_vehicle_snapshots')
                        ->whereIn('vehicle_id', $ids)
                        ->get([
                            'vehicle_id',
                            'code',
                            'licence_plate',
                        ])
                        ->mapWithKeys(fn ($vehicle) => [
                            'vehicle:' . $vehicle->vehicle_id => [
                                'label' => $vehicle->code
                                    ?: ($vehicle->licence_plate ?: (string) $vehicle->vehicle_id),
                                'description' => '',
                            ],
                        ])
                        ->all();

                    break;

                default:
                    // Keep a usable fallback for subject types that don't
                    // yet have a label resolver.
                    foreach ($ids as $id) {
                        $labels["{$type}:{$id}"] = [
                            'label' => (string) $id,
                            'description' => '',
                        ];
                    }
            }
        }

        return $labels;
    }

    /**
     * @param array<int|string> $ids
     * @return array<int, string>
     */
    public function resolveTaskItemGroupLabels(array $ids): array
    {
        $ids = collect($ids)->map(fn ($id) => (int) $id)->unique();

        if ($ids->isEmpty()) {
            return [];
        }

        return TaskItemGroup::query()
            ->whereIn('id', $ids)
            ->pluck('title', 'id')
            ->all();
    }

    /**
     * @param array<int|string> $ids
     * @return array<int, string>
     */
    public function resolveContractLabels(array $ids): array
    {
        $ids = collect($ids)->map(fn ($id) => (int) $id)->unique();

        if ($ids->isEmpty()) {
            return [];
        }

        return DB::table('mvw_hr_contract_snapshots')
            ->whereIn('contract_id', $ids)
            ->get([
                'contract_id',
                'pid',
                'last_name',
            ])
            ->mapWithKeys(fn ($contract) => [
                $contract->contract_id => $contract->pid
                    . ' - '
                    . $contract->last_name,
            ])
            ->all();
    }
}