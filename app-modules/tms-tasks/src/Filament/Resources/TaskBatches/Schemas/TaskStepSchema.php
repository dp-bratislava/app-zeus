<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Carbon\Carbon;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\AssignedToField;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskGroupPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehiclePicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskItemGroupPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehicleTypePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;

class TaskStepSchema
{
    public static function make(): array
    {
        return [
            Grid::make(9)
                ->columnSpanFull()
                ->schema([
                    // date
                    DatePicker::make('date')
                        ->label(__('dpb-mod-tasks::task-batch.form.fields.date'))
                        ->default(Carbon::now())
                        ->required()
                        ->columnSpan(2),
                    // task item groups
                    TaskGroupPicker::make()
                        ->columnSpan(7),
                    TaskItemGroupPicker::make()
                        ->columnSpan(9),
                ]),

            // maintenance group
            Grid::make(6)
                ->columnSpanFull()
                ->schema([
                    AssignedToField::make(),
                    VehicleTypePicker::make()
                    ->columnSpan(4),
                ]),

            Grid::make(1)
                ->columnSpanFull()
                ->schema([
                    VehiclePicker::make()
                        ->label(__('dpb-mod-tasks::task-batch.form.fields.vehicles'))
                ]),
        ];
    }
}
