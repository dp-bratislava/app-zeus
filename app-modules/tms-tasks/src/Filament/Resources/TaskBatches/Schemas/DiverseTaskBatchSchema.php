<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\ContractPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskBatchConfiguration;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehicleSelect;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;

class DiverseTaskBatchSchema
{
    public const ASSIGNMENT_MATRIX_STATE_KEY = 'assignment-matrix-state';
    public const NOTES_STATE_KEY = 'notes-state';

    public static function make(): array
    {
        return [
            Hidden::make(self::ASSIGNMENT_MATRIX_STATE_KEY)
                ->default([])
                ->dehydrated(),
            Hidden::make(self::NOTES_STATE_KEY)
                ->default([])
                ->dehydrated(),

            Grid::make(1)
                ->columnSpanFull()
                ->schema([
                    ContractPicker::make(TaskBatchForm::CONTRACT_PICKER_KEY)
                        ->label(__('dpb-mod-tasks::task-batch.form.fields.contracts'))
                        ->afterStateUpdated(function ($state, $get, $set): void {
                            $selectedContractIds = array_map('intval', $state ?? []);

                            $matrixState = $get(
                                self::ASSIGNMENT_MATRIX_STATE_KEY,
                            ) ?? [];

                            foreach ($matrixState as &$groups) {
                                foreach ($groups as &$cell) {
                                    $cell['work'] = array_values(array_filter(
                                        $cell['work'] ?? [],
                                        fn(array $work): bool => in_array(
                                            (int) $work['contract_id'],
                                            $selectedContractIds,
                                            true,
                                        ),
                                    ));
                                }

                                unset($cell);
                            }

                            unset($groups);

                            $set(
                                self::ASSIGNMENT_MATRIX_STATE_KEY,
                                $matrixState,
                            );
                        }),
                    VehicleSelect::make(TaskBatchForm::SUBJECT_PICKER_KEY)
                        ->label(__('dpb-mod-tasks::task-batch.form.fields.vehicles'))
                        ->afterStateUpdated(function ($state, $get, $set): void {
                            $selectedVehicleIds = array_map(
                                'intval',
                                $state ?? [],
                            );

                            $matrixState = $get(
                                self::ASSIGNMENT_MATRIX_STATE_KEY,
                            ) ?? [];

                            // Keep assignments only for currently selected vehicles.
                            $filteredState = array_intersect_key(
                                $matrixState,
                                array_flip($selectedVehicleIds),
                            );

                            $set(
                                self::ASSIGNMENT_MATRIX_STATE_KEY,
                                $filteredState,
                            );
                        })
                ]),
            Grid::make(1)
                ->columnSpanFull()
                ->schema([
                    TaskBatchConfiguration::make()
                ]),
        ];
    }
}
