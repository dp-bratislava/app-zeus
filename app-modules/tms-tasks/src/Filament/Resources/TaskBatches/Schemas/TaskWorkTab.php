<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Carbon\Carbon;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskGroupPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskItemGroupPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\ContractPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskItemGroupOperationsPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs\Tab;

class TaskWorkTab
{
    public static function make(): Component
    {
        return Tab::make('praca')
            ->schema([
                Grid::make(9)
                    ->columnSpanFull()
                    ->schema([
                        // date
                        DatePicker::make(TaskBatchForm::COL_NAME_DATE)
                            ->label(__('dpb-mod-tasks::task-batch.form.fields.date'))
                            ->default(Carbon::now())
                            ->required()
                            ->live()
                            ->columnSpan(2),
                        TaskGroupPicker::make()
                            ->columnSpan(7),
                        // TaskItemGroupPicker::make()
                        TaskItemGroupOperationsPicker::make()
                            ->columnSpan(9),
                        ContractPicker::make()
                            ->label(__('dpb-mod-tasks::task-batch.form.fields.contracts')),
                    ]),
            ]);
    }
}
