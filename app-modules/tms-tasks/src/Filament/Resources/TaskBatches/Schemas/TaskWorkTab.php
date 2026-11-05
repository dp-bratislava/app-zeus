<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\ContractPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskItemGroupOperationsPicker;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs\Tab;

class TaskWorkTab
{
    public static function make(): Component
    {
        return Tab::make(__('dpb-mod-tasks::task-batch.form.tabs.work'))
            ->schema([
                Grid::make(9)
                    ->columnSpanFull()
                    ->schema([
                        TaskItemGroupOperationsPicker::make()
                            ->columnSpan(9),
                        ContractPicker::make(TaskBatchForm::CONTRACT_PICKER_KEY)
                            ->label(__('dpb-mod-tasks::task-batch.form.fields.contracts')),
                    ]),
            ]);
    }
}
