<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use App\Models\Datahub\EmployeeContract;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskGroupPicker;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskGroupModeResolver;
use Dpb\Package\Tasks\Models\TaskGroup;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TaskBatchForm
{
    public const FIELD_NAME_ASSIGNMENT_MATRIX_STATE = 'assignment-matrix-state';
    public const FIELD_NAME_NOTES_STATE = 'notes-state';
    public const FIELD_NAME_VEHICLE_PICKER = 'vehicles';
    public const COL_NAME_CONTRACT_PICKER = 'contracts';
    public const COL_NAME_DATE = 'date';
    public const COL_NAME_OPERATION_PICKER = 'operations-by-task-item-group';
    public const COL_NAME_VEHICLE_TYPE_PICKER = 'vehicle-type-id';
    public const COL_NAME_ASSIGNED_TO = 'assigned-to-id';
    public const COL_NAME_TG_PICKER = 'task-groups';
    public const COL_NAME_TIG_PICKER = 'task-item-groups';
    public const COMPONENT_NAME_TASK_BATCH_CONFIG = 'task-batch-config';

    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema
            ->columns(12)
            ->components([
                DatePicker::make(TaskBatchForm::COL_NAME_DATE)
                    ->label(__('dpb-mod-tasks::task-batch.form.fields.date'))
                    ->default(Carbon::now())
                    ->required()
                    ->live()
                    ->columnSpan(2),
                TaskGroupPicker::make(TaskBatchForm::COL_NAME_TG_PICKER)
                    ->columnSpan(7),

                Group::make()
                    ->columnSpan(12)
                    ->schema(function (Get $get): array {
                        $tgId = $get(TaskBatchForm::COL_NAME_TG_PICKER);

                        if (! $tgId) {
                            return [];
                        }
                        $departmentId = app(DepartmentService::class)->getActiveDepartment()->id;
                        // dd($departmentId, $tgId);
                        $mode = app(TaskGroupModeResolver::class)
                            ->resolve($tgId, $departmentId);

                        return match ($mode) {
                            TaskBatchMode::Diverse => DiverseTaskBatchSchema::make(),
                            TaskBatchMode::Uniform => UniformTaskBatchSchema::make(),
                            default => []
                        };
                    }),
            ]);
    }
}
