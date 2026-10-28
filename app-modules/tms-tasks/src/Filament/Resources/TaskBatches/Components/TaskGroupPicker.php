<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class TaskGroupPicker
{
    public static function make(
        ?string $name = TaskBatchForm::COL_NAME_TG_PICKER
    ): Component {
        return ToggleButtons::make($name)
            ->label(__('dpb-mod-tasks::task-batch.form.fields.task_groups'))
            ->inline()
            ->required()
            ->live()
            ->options(
                fn(
                    TaskBatchLookupScopeService $lookupService
                ): array => $lookupService
                    ->taskGroups()
                    ->pluck('title', 'id')
                    ->toArray()
            )
            ->default(
                function (
                    TaskBatchLookupScopeService $lookupService
                ) {
                    $taskGroups = $lookupService->taskGroups();

                    return $taskGroups->count() === 1
                        ? $taskGroups->first()->id
                        : null;
                }
            )
            ->afterStateUpdated(function (
                TaskBatchLookupScopeService $lookupService,
                Get $get,
                Set $set
            ) {
                $taskGroupId = $get(TaskBatchForm::COL_NAME_TG_PICKER);

                $taskItemGroups = $lookupService
                    ->taskItemGroups()
                    ->filter(
                        fn($group) => (int) $group->task_group_id === (int) $taskGroupId
                    );

                $default = $taskItemGroups->count() === 1
                    ? [$taskItemGroups->first()->id]
                    : [];

                $set(TaskBatchForm::COL_NAME_TIG_PICKER, $default);
            });
    }
}
