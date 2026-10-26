<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Livewire\AssignmentMatrix;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\DB;

class TaskBatchConfiguration
{
    public static function make(
        ?string $name = TaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG,
    ): Component {
        return Section::make()
            ->schema(function (Get $get): array {
                $selectedContractIds = array_map(
                    'intval',
                    $get(TaskBatchForm::COL_NAME_CONTRACT_PICKER) ?? [],
                );

                $allContracts = app(TaskBatchLookupScopeService::class)
                    // ->employeeContracts(app(TaskBatchResourceContext::class));
                    ->employeeContracts(
                        CarbonImmutable::parse(
                            $get(TaskBatchForm::COL_NAME_DATE)
                        )
                    );

                $contractOptions = array_intersect_key(
                    $allContracts,
                    array_flip($selectedContractIds),
                );

                $selectedVehicleIds = array_map(
                    'intval',
                    $get(TaskBatchForm::FIELD_NAME_VEHICLE_PICKER) ?? [],
                );

                $subjects = self::getSubjects($selectedVehicleIds);

                $groups = app(TaskBatchLookupScopeService::class)
                    ->batchableTaskItemGroups()
                    ->map(fn($group) => [
                        'id' => (int) $group->task_item_group_id,
                        'label' => $group->label,
                        'is_scalable' => $group->is_scalable,
                    ])
                    ->values()
                    ->all();

                $state = self::filterMatrixState(
                    $get(TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE) ?? [],
                    $selectedVehicleIds,
                );

                // dd($groups);
                return [
                    Livewire::make(AssignmentMatrix::class, [
                        'subjects' => $subjects,
                        'groups' => $groups,
                        'state' => $state,
                        'contractOptions' => $contractOptions,
                    ])
                        ->key('assignment-matrix-' . md5(
                            json_encode([
                                $selectedVehicleIds,
                                $selectedContractIds
                            ])
                        )),
                ];
            });
    }

    private static function getSubjects(array $selectedVehicleIds): array
    {
        if ($selectedVehicleIds === []) {
            return [];
        }

        return DB::table('mvw_fleet_vehicle_snapshots')
            ->whereIn('vehicle_id', $selectedVehicleIds)
            ->get(['vehicle_id', 'code', 'licence_plate'])
            ->map(fn($vehicle) => [
                'id' => (int) $vehicle->vehicle_id,
                'label' => $vehicle->code
                    ?? $vehicle->licence_plate
                    ?? (string) $vehicle->vehicle_id,
            ])
            ->values()
            ->all();
    }

    private static function filterMatrixState(
        array $state,
        array $selectedVehicleIds,
    ): array {
        $selectedIds = array_flip($selectedVehicleIds);

        return array_intersect_key($state, $selectedIds);
    }
}
