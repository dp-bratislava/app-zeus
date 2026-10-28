<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\AssignedToField;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehiclePicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehicleTypePicker;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs\Tab;

class TaskSubjectTab
{
    public static function make(): Component
    {
        return Tab::make(__('dpb-mod-tasks::task-batch.form.tabs.subjects'))
            ->schema([
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
            ]);
    }
}
