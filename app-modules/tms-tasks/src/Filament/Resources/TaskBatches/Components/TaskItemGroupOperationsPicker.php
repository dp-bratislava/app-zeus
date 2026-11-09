<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations\OperationOptions;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations\TaskItemGroupOptions;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\UniformTaskBatchSchema;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

final class TaskItemGroupOperationsPicker
{
    private static function operationsPickers(
        int $departmentId
    ): Component {
        return Section::make(
            __('dpb-mod-tasks::task-batch.form.fields.operations')
        )
            ->schema(function (Get $get) use ($departmentId): array {
                return collect(
                    $get(UniformTaskBatchSchema::TIG_PICKER_KEY) ?? []
                )
                    ->map(fn($tigId) => self::operationPicker($departmentId, (int) $tigId))
                    ->values()
                    ->all();
            })
            ->key(UniformTaskBatchSchema::OPERATION_PICKER_CONTAINER_KEY);
    }

    private static function operationPicker(
        int $departmentId,
        int $tigId
    ): Component {

        return ToggleButtons::make(
            UniformTaskBatchSchema::OPERATION_PICKER_KEY . '.' . $tigId
        )
            ->key("operation-picker-{$tigId}")
            ->label(
                fn(Get $get, TaskItemGroupOptions $options): string =>
                $options->labelFor(
                    departmentId: $departmentId,
                    taskItemGroupId: $tigId,
                    taskGroupId: (int) $get(TaskBatchForm::TASK_GROUP_PICKER_KEY),
                )
            )
            ->multiple()
            ->options(
                function (OperationOptions $options) use ($departmentId, $tigId): array {
                    return $options->for(
                        departmentId: $departmentId,
                        taskItemGroupId: $tigId,
                    );
                }
            )
            ->inline();
    }

    private static function taskItemGroupPicker(int $departmentId): Component
    {
        return ToggleButtons::make(
            UniformTaskBatchSchema::TIG_PICKER_KEY
        )
            ->label(__('dpb-mod-tasks::task-batch.form.fields.task_item_groups'))
            ->multiple()
            ->options(
                fn(Get $get, TaskItemGroupOptions $options): array =>
                $options->for(
                    $departmentId,
                    (int) $get(TaskBatchForm::TASK_GROUP_PICKER_KEY)
                )
            )
            ->live()
            ->inline()
            ->afterStateUpdated(function (
                Get $get,
                Set $set,
                ToggleButtons $component,
                OperationOptions $operationOptions,
            ) use ($departmentId): void {
                $selectedIds = collect(
                    $get(UniformTaskBatchSchema::TIG_PICKER_KEY) ?? []
                )
                    ->map(fn($id) => (string) $id)
                    ->all();

                $currentOperations = $get(
                    UniformTaskBatchSchema::OPERATION_PICKER_KEY
                ) ?? [];

                // Preserve selections for groups that remain selected.
                $operations = array_intersect_key(
                    $currentOperations,
                    array_flip($selectedIds),
                );

                // Initialise newly selected groups.
                foreach ($selectedIds as $tigId) {
                    if (array_key_exists($tigId, $operations)) {
                        continue;
                    }

                    $availableOperations = $operationOptions->for(
                        departmentId: $departmentId,
                        taskItemGroupId: (int) $tigId,
                    );

                    $operations[$tigId] = count($availableOperations) === 1
                        ? [(int) array_key_first($availableOperations)]
                        : [];
                }

                // Initialise dynamically generated fields.
                $component
                    ->getContainer()
                    ->getComponent(
                        UniformTaskBatchSchema::OPERATION_PICKER_CONTAINER_KEY
                    )
                    ->getChildSchema()
                    ->fill();

                // Restore preserved selections and initialise new ones.
                $set(
                    UniformTaskBatchSchema::OPERATION_PICKER_KEY,
                    $operations,
                );
            });
    }

    public static function make(): Component
    {
        $departmentId = app(DepartmentService::class)
            ->getActiveDepartment()
            ->id;

        return Group::make()
            ->schema([
                self::taskItemGroupPicker($departmentId),
                self::operationsPickers($departmentId),
            ]);
    }
}
