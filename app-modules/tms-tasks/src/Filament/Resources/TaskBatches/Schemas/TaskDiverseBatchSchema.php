<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Carbon\Carbon;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\AssignedToField;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskGroupPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehiclePicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskItemGroupPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskBatchConfig;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehicleTypePicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\ContractPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskBatchConfiguration;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehicleSelect;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;

class TaskDiverseBatchSchema
{
    public static function make(): array
    {
        return [
            Hidden::make(TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE)
                ->default([])
                ->dehydrated(),
            Hidden::make(TaskBatchForm::FIELD_NAME_NOTES_STATE)
                ->default([])
                ->dehydrated(),

            Grid::make(9)
                ->columnSpanFull()
                ->schema([
                    // date
                    DatePicker::make('date')
                        ->label(__('dpb-mod-tasks::task-batch.form.fields.date'))
                        ->default(Carbon::now())
                        ->required()
                        ->columnSpan(2),
                ]),

            Grid::make(1)
                ->columnSpanFull()
                ->schema([
                    ContractPicker::make()
                        ->label(__('dpb-mod-tasks::task-batch.form.fields.contracts')),
                    VehicleSelect::make()
                        ->label(__('dpb-mod-tasks::task-batch.form.fields.vehicles')),
                ]),

            // Grid::make(1)
            //     ->columnSpanFull()
            //     ->schema([
            //         TaskBatchConfig::make()
            //             ->label(__('dpb-mod-tasks::task-batch.form.fields.vehicles'))
            //     ]),

            Grid::make(1)
                ->columnSpanFull()
                ->schema([
                    TaskBatchConfiguration::make()
                ]),

            // Livewire::make('tms-tasks.assignment-matrix', [
            //     'subjects' => $subjects,
            //     'groups' => $groups,
            // ]),
        ];
    }
}
