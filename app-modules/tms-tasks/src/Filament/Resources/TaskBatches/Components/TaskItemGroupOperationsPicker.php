<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations\OperationOptions;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations\TaskItemGroupOptions;
use Dpb\WtfTmsBridge\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

final class TaskItemGroupOperationsPicker
{
    public static function make(): Component
    {
        return Group::make()
            ->schema([
                ToggleButtons::make('selected_task_item_groups')
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
                        $selected = collect($get('selected_task_item_groups') ?? [])
                            ->map(fn($id) => (int) $id)
                            ->all();

                        // $operations = $get('task_item_groups') ?? [];
                        $operations = $get(TaskBatchForm::COL_NAME_TIG_PICKER) ?? [];

                        $set(
                            'task_item_groups',
                            collect($operations)
                                ->only($selected)
                                ->all(),
                        );
                    }),

                Group::make()
                    ->schema(function (Get $get): array {
                        $taskGroupId = $get(TaskBatchForm::COL_NAME_TG_PICKER);

                        return collect($get('selected_task_item_groups') ?? [])
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
                                                $get('../../../../subjects') ?? [],
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
