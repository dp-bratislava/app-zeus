<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Filament\Schemas\Components\Tabs;

class UniformTaskBatchSchema
{
    public const MAINTENANCE_GROUP_FILTER_KEY = 'maintenance-group';
    public const OPERATION_PICKER_KEY = 'operations-by-task-item-group';
    public const OPERATION_PICKER_CONTAINER_KEY = 'operation-picker-container';
    public const TIG_PICKER_KEY = 'task-item-groups';
    public const VEHICLE_TYPE_FILTER_KEY = 'vehicle-type-id';

    public static function make(): array
    {
        return [
            Tabs::make('Tabs')
                ->tabs([
                    TaskWorkTab::make(),
                    TaskSubjectTab::make()
                ])
        ];
    }
}
