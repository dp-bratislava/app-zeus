<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Carbon\Carbon;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\ContractPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskBatchConfiguration;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehicleSelect;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;

class DiverseTaskBatchSchema
{
    public static function make(): array
    {
        return [
            Hidden::make(DiverseTaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE)
                ->default([])
                ->dehydrated(),
            Hidden::make(DiverseTaskBatchForm::FIELD_NAME_NOTES_STATE)
                ->default([])
                ->dehydrated(),

            // Grid::make(9)
            //     ->columnSpanFull()
            //     ->schema([
            //         // date
            //         DatePicker::make(DiverseTaskBatchForm::COL_NAME_DATE)
            //             ->label(__('dpb-mod-tasks::task-batch.form.fields.date'))
            //             ->default(Carbon::now())
            //             ->required()
            //             ->live()
            //             ->columnSpan(2),
            //     ]),

            Grid::make(1)
                ->columnSpanFull()
                ->schema([
                    ContractPicker::make(DiverseTaskBatchForm::COL_NAME_CONTRACT_PICKER)
                        ->label(__('dpb-mod-tasks::task-batch.form.fields.contracts')),
                    VehicleSelect::make(DiverseTaskBatchForm::FIELD_NAME_VEHICLE_PICKER)
                        ->label(__('dpb-mod-tasks::task-batch.form.fields.vehicles')),
                ]),
            Grid::make(1)
                ->columnSpanFull()
                ->schema([
                    TaskBatchConfiguration::make(DiverseTaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG)
                ]),
        ];
    }
}
