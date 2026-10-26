<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class ContractPicker
{
    public static function make(
        ?string $name = TaskBatchForm::COL_NAME_CONTRACT_PICKER
    ): Component {
        return ToggleButtons::make($name)
            ->columnSpanFull()
            ->extraAttributes([
                'class' => 'grid grid-cols-8 gap-2 w-full [&>div>label]:w-full [&>div>label>span]:w-full',
            ])
            // ->columns(12)
            // ->fullWidth()
            ->inline()
            ->options(fn(TaskBatchLookupScopeService $lookupSvc, Get $get) => $lookupSvc->employeeContracts(
                CarbonImmutable::parse(
                    $get(TaskBatchForm::COL_NAME_DATE)
                )
            ))
            ->multiple()
            ->live()
            ->afterStateUpdated(function ($state, $get, $set): void {
                $selectedContractIds = array_map('intval', $state ?? []);

                $matrixState = $get(
                    TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE,
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
                    TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE,
                    $matrixState,
                );
            });
    }
}
