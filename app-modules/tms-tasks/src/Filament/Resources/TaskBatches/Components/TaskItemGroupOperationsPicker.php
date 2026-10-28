<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations\OperationOptions;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations\TaskItemGroupOptions;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

final class TaskItemGroupOperationsPicker
{
    private static function OperationsPicker(): Component
    {
        $tigId = 45;
        return Group::make()
            ->schema([
                ToggleButtons::make(TaskBatchForm::COL_NAME_OPERATION_PICKER . "." . $tigId)
                    ->label(__('dpb-mod-tasks::task-batch.form.fields.operation'))
                    ->multiple()
                    ->options(
                        fn(Get $get, OperationOptions $options): array =>
                        $options->forTaskItemGroup(
                            $tigId
                        )
                    )
            ]);
    }

    private static function taskItemGroupPicker(): Component
    {
        return ToggleButtons::make(TaskBatchForm::COL_NAME_TIG_PICKER)
            ->label(__('dpb-mod-tasks::task-batch.form.fields.task_item_groups'))
            ->multiple()
            ->options(
                fn(Get $get, TaskItemGroupOptions $options): array =>
                $options->forTaskGroup(
                    // (int) $get('../../' . TaskBatchForm::COL_NAME_TG_PICKER),
                    (int) $get(TaskBatchForm::COL_NAME_TG_PICKER),
                )
            )
            ->live()
            ->inline()
            ->afterStateUpdated(function (Get $get, Set $set): void {
                $selected = collect(
                    $get(TaskBatchForm::COL_NAME_TIG_PICKER) ?? []
                )->map(fn($id) => (int) $id);

                $operations = $get(
                    TaskBatchForm::COL_NAME_OPERATION_PICKER
                ) ?? [];

                // $set(
                //     TaskBatchForm::COL_NAME_OPERATION_PICKER,
                //     collect($operations)
                //         ->only($selected->all())
                //         ->all(),
                // );
            });
    }

    public static function make(): Component
    {
        return Group::make()
            ->schema([
                self::taskItemGroupPicker(),
                self::OperationsPicker(),
            ]);
    }

    public static function make3(): Component
    {
        $tigId = 45;
        return Group::make()
            ->schema([
                ToggleButtons::make(TaskBatchForm::COL_NAME_OPERATION_PICKER . "." . $tigId)
                    ->label(__('dpb-mod-tasks::task-batch.form.fields.task_item_groups'))
                    ->multiple()
                    ->options(
                        fn(Get $get, OperationOptions $options): array =>
                        $options->forTaskItemGroup(
                            $tigId
                        )
                    )
            ]);
    }

    public static function make2(): Component
    {
        return Group::make()
            ->schema([
                ToggleButtons::make(TaskBatchForm::COL_NAME_TIG_PICKER)
                    ->label(__('dpb-mod-tasks::task-batch.form.fields.task_item_groups'))
                    ->multiple()
                    ->options(
                        fn(Get $get, TaskItemGroupOptions $options): array =>
                        $options->forTaskGroup(
                            // (int) $get('../../' . TaskBatchForm::COL_NAME_TG_PICKER),
                            (int) $get(TaskBatchForm::COL_NAME_TG_PICKER),
                        )
                    )
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set): void {
                        $selected = collect(
                            $get(TaskBatchForm::COL_NAME_TIG_PICKER) ?? []
                        )->map(fn($id) => (int) $id);

                        $operations = $get(
                            TaskBatchForm::COL_NAME_OPERATION_PICKER
                        ) ?? [];

                        $set(
                            TaskBatchForm::COL_NAME_OPERATION_PICKER,
                            collect($operations)
                                ->only($selected->all())
                                ->all(),
                        );
                    }),

                Group::make()
                    ->schema(function (Get $get): array {
                        $taskGroupId = (int) $get(
                            TaskBatchForm::COL_NAME_TG_PICKER
                        );

                        return collect(
                            $get(TaskBatchForm::COL_NAME_TIG_PICKER) ?? []
                        )
                            ->map(function ($taskItemGroupId) use ($taskGroupId) {
                                $taskItemGroupId = (int) $taskItemGroupId;

                                return Group::make()
                                    ->key("task-item-group-{$taskItemGroupId}")
                                    ->schema([
                                        // ToggleButtons::make(
                                        //     // TaskBatchForm::COL_NAME_OPERATION_PICKER

                                        //     "operations_by_group.{$taskItemGroupId}"
                                        // )
                                        //     ->statePath("operations_by_group.{$taskItemGroupId}")
                                        //     ->label(
                                        //         app(TaskItemGroupOptions::class)
                                        //             ->labelFor(
                                        //                 $taskItemGroupId,
                                        //                 $taskGroupId,
                                        //             )
                                        //     )
                                        //     ->multiple()
                                        //     ->options(
                                        //         fn(OperationOptions $options): array =>
                                        //         // dd($options->forTaskItemGroup($taskItemGroupId))
                                        //         $options->forTaskItemGroup(
                                        //             $taskItemGroupId,
                                        //         )
                                        //     )
                                        //     ->live()
                                        ToggleButtons::make('test_operations')
                                            ->multiple()
                                            ->options([
                                                2900100 => 'DO 5',
                                                2900101 => 'DO 8',
                                                2900102 => 'DO 10',
                                            ])
                                            ->afterStateUpdated(function ($state) {
                                                dd($state);
                                            })
                                    ]);
                            })
                            ->values()
                            ->all();
                    }),
            ]);
    }

    public static function make1(): Component
    {
        return Group::make()
            ->schema([
                ToggleButtons::make(TaskBatchForm::COL_NAME_TIG_PICKER)
                    ->label(__('dpb-mod-tasks::task-batch.form.fields.task_item_groups'))
                    ->multiple()
                    ->options(
                        fn(Get $get, TaskItemGroupOptions $options): array =>
                        $options->forTaskGroup(
                            // (int) $get('../../task_group_id'),
                            (int) $get(TaskBatchForm::COL_NAME_TG_PICKER),
                        )
                    )
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set): void {
                        $selected = collect($get(TaskBatchForm::COL_NAME_TIG_PICKER) ?? [])
                            ->map(fn($id) => (int) $id)
                            ->all();

                        // $operations = $get('task_item_groups') ?? [];
                        $operations = $get(TaskBatchForm::COL_NAME_OPERATION_PICKER) ?? [];

                        $set(
                            TaskBatchForm::COL_NAME_OPERATION_PICKER,
                            collect($operations)
                                ->only($selected)
                                ->all(),
                        );
                    }),

                Group::make()
                    ->schema(function (Get $get): array {
                        $taskGroupId = $get(TaskBatchForm::COL_NAME_TG_PICKER);

                        return collect($get(TaskBatchForm::COL_NAME_TIG_PICKER) ?? [])
                            ->map(
                                fn($taskItemGroupId) =>
                                ToggleButtons::make("task_item_groups.{$taskItemGroupId}")
                                    ->label(
                                        app(TaskItemGroupOptions::class)
                                            ->labelFor((int) $taskItemGroupId, $taskGroupId)
                                    )
                                    ->multiple()
                                    ->options(
                                        fn() =>
                                        app(OperationOptions::class)
                                            ->forTaskItemGroup(
                                                (int) $taskItemGroupId,
                                            )
                                    )
                                    ->live()
                            )
                            ->values()
                            ->all();
                    }),
            ]);
    }
}
