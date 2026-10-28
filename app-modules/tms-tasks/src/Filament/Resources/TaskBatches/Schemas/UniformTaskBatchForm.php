<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Filament\Schemas\Schema;

class UniformTaskBatchForm
{
    public const FIELD_NAME_VEHICLE_PICKER = 'vehicles';
    public const COL_NAME_CONTRACT_PICKER = 'contracts';
    public const COL_NAME_DATE = 'date';
    public const COL_NAME_OPERATION_PICKER = 'operations-by-task-item-group';
    public const COL_NAME_VEHICLE_TYPE_PICKER = 'vehicle-type-id';
    public const COL_NAME_ASSIGNED_TO = 'assigned-to-id';
    public const COL_NAME_TG_PICKER = 'task-groups';
    public const COL_NAME_TIG_PICKER = 'task-item-groups';

    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema
            ->columns(1)            
            ->components(
                UniformTaskBatchSchema::make(),
            );
    }
}
