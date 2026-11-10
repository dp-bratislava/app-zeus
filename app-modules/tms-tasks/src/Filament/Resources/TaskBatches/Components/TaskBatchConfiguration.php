<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\DiverseTaskBatchSchema;
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
    public static function make(): Component
    {
        return Section::make()
            ->schema(function (Get $get): array {
                $selectedContractIds = array_map(
                    'intval',
                    $get(TaskBatchForm::CONTRACT_PICKER_KEY) ?? [],
                );

                $contractOptions = app(TaskBatchLookupScopeService::class)
                    ->employeeContracts(
                        CarbonImmutable::parse(
                            $get(TaskBatchForm::DATE_KEY)
                        )
                    )
                    ->mapWithKeys(fn($contract) => [
                        (int) $contract->contract_id =>
                        $contract->pid . ' - ' . $contract->last_name,
                    ])
                    ->only($selectedContractIds)
                    ->all();

                $selectedVehicleIds = array_map(
                    'intval',
                    $get(TaskBatchForm::SUBJECT_PICKER_KEY) ?? [],
                );

                $subjects = self::getSubjects($selectedVehicleIds);

                $groups = app(TaskBatchLookupScopeService::class)
                    ->taskItemGroups()
                    ->map(fn($group) => [
                        'id' => (int) $group->id,
                        'label' => $group->label,
                        'is_scalable' => $group->is_scalable,
                    ])
                    ->values()
                    ->all();

                $matrixState = self::filterMatrixState(
                    $get(DiverseTaskBatchSchema::ASSIGNMENT_MATRIX_STATE_KEY) ?? [],
                    $selectedVehicleIds,
                );


                $notesState = self::filterNotesState(
                    $get(DiverseTaskBatchSchema::NOTES_STATE_KEY) ?? [],
                    $selectedVehicleIds,
                );

                return [
                    Livewire::make(AssignmentMatrix::class, [
                        'subjects' => $subjects,
                        'groups' => $groups,
                        'state' => $matrixState,
                        'contractOptions' => $contractOptions,
                        'notes' => $notesState,
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

    private static function filterNotesState(
        array $state,
        array $selectedVehicleIds,
    ): array {
        $selectedIds = array_flip($selectedVehicleIds);

        return array_intersect_key($state, $selectedIds);
    }
}
