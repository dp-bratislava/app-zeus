<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use App\Models\Datahub\EmployeeContract;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class TaskBatchForm
{
    public const IS_UNIFORM = true;
    public const IS_DIVERSE = false;
    public const FIELD_NAME_ASSIGNMENT_MATRIX_STATE = 'assignment_matrix_state';
    public const FIELD_NAME_NOTES_STATE = 'notes_state';
    public const FIELD_NAME_VEHICLE_PICKER = 'vehicles';
    public const COL_NAME_CONTRACT_PICKER = 'contracts';
    public const COL_NAME_DATE = 'date';
    public const COL_NAME_OPERATION_PICKER = 'operations';
    public const COL_NAME_VEHICLE_TYPE_PICKER = 'vehicle_type_id';
    public const COL_NAME_ASSIGNED_TO = 'assigned_to_id';
    public const COL_NAME_TG_PICKER = 'task-groups';
    public const COL_NAME_TIG_PICKER = 'task-item-groups';
    public const COMPONENT_NAME_TASK_BATCH_CONFIG = 'task-batch-config';

    public static function configure(
        Schema $schema,
        bool $isUniform
    ): Schema {
        if ($isUniform) {
            return $schema
                ->components([
                    ...UniformTaskBatchSchema::make(),
                ]);
        }

        return $schema
            ->components([
                ...DiverseTaskBatchSchema::make(),
            ]);
    }
}
