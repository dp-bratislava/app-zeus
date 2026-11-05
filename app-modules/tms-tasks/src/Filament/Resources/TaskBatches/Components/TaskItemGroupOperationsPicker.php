<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

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
    private static function operationsPicker1(): Component
    {
        return Group::make()
            ->schema(function (Get $get): array {
                $selected = $get(
                    UniformTaskBatchSchema::TIG_PICKER_KEY
                ) ?? [];

                return collect($selected)
                    ->map(fn($tigId) => self::operationPicker((int) $tigId))
                    ->all();
            })
            ->key(UniformTaskBatchSchema::OPERATION_PICKER_CONTAINER_KEY);
    }

    private static function operationsPickers(): Component
    {
        return Section::make(
            __('dpb-mod-tasks::task-batch.form.fields.operations')
        )
            ->schema(function (Get $get): array {
                return collect(
                    $get(UniformTaskBatchSchema::TIG_PICKER_KEY) ?? []
                )
                    ->map(fn($tigId) => self::operationPicker((int) $tigId))
                    ->values()
                    ->all();
            })
            // ->key('dynamicOperationPickers');
            ->key(UniformTaskBatchSchema::OPERATION_PICKER_CONTAINER_KEY);
    }

    private static function operationPicker(int $tigId): Component
    {
        return ToggleButtons::make(
            UniformTaskBatchSchema::OPERATION_PICKER_KEY . '.' . $tigId
        )
            ->key("operation-picker-{$tigId}")
            ->label(
                fn(Get $get, TaskItemGroupOptions $options): string =>
                $options->labelFor(
                    $tigId,
                    (int) $get(TaskBatchForm::TASK_GROUP_PICKER_KEY),
                )
            )
            ->multiple()
            ->options(
                fn(OperationOptions $options): array =>
                $options->forTaskItemGroup($tigId)
            )
            ->inline();
    }

    private static function taskItemGroupPicker(): Component
    {
        return ToggleButtons::make(
            UniformTaskBatchSchema::TIG_PICKER_KEY
        )
            ->label(__('dpb-mod-tasks::task-batch.form.fields.task_item_groups'))
            ->multiple()
            ->options(
                fn(Get $get, TaskItemGroupOptions $options): array =>
                $options->forTaskGroup(
                    (int) $get(TaskBatchForm::TASK_GROUP_PICKER_KEY)
                )
            )
            ->live()
            ->inline()
            ->afterStateUpdated(function (
                Get $get,
                Set $set,
                ToggleButtons $component,
            ): void {
                $selectedIds = collect(
                    $get(UniformTaskBatchSchema::TIG_PICKER_KEY) ?? []
                )
                    ->map(fn($id) => (string) $id)
                    ->all();

                // Preserve operation selections for groups that remain selected.
                $currentOperations = $get(
                    UniformTaskBatchSchema::OPERATION_PICKER_KEY
                ) ?? [];

                $preservedOperations = array_intersect_key(
                    $currentOperations,
                    array_flip($selectedIds),
                );

                // Initialise only newly selected groups.
                foreach ($selectedIds as $id) {
                    $preservedOperations[$id] ??= [];
                }

                // Initialise the newly generated dynamic fields.
                $component
                    ->getContainer()
                    ->getComponent(UniformTaskBatchSchema::OPERATION_PICKER_CONTAINER_KEY)
                    ->getChildSchema()
                    ->fill();

                // Restore selections after fill().
                $set(
                    UniformTaskBatchSchema::OPERATION_PICKER_KEY,
                    $preservedOperations,
                );
            });
    }

    public static function make(): Component
    {
        return Group::make()
            ->schema([
                self::taskItemGroupPicker(),
                self::operationsPickers(),
            ]);
    }
}
