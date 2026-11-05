<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\TaskGroupPicker;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskGroupModeResolver;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class TaskBatchForm
{
    public const FIELD_NAME_ASSIGNMENT_MATRIX_STATE = 'assignment-matrix-state';
    public const CONTRACT_PICKER_KEY = 'contracts';
    public const DATE_KEY = 'date';
    public const SUBJECT_PICKER_KEY = 'vehicles';
    public const TASK_GROUP_PICKER_KEY = 'task-groups';
    public const COL_NAME_OPERATION_PICKER = 'operations-by-task-item-group';
    public const COL_NAME_TIG_PICKER = 'task-item-groups';

    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema
            ->columns(12)
            ->components([
                DatePicker::make(TaskBatchForm::DATE_KEY)
                    ->label(__('dpb-mod-tasks::task-batch.form.fields.date'))
                    ->default(Carbon::now())
                    ->required()
                    ->live()
                    ->columnSpan(2),
                TaskGroupPicker::make(TaskBatchForm::TASK_GROUP_PICKER_KEY)
                    ->columnSpan(7),

                Group::make()
                    ->columnSpan(12)
                    ->schema(function (Get $get): array {
                        $tgId = $get(TaskBatchForm::TASK_GROUP_PICKER_KEY);

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
