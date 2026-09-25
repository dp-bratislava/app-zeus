<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class TaskGroupPicker
{
    public static function make(?string $name = TaskBatchForm::COL_NAME_TG_PICKER): Component
    {
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

                    if ($taskGroups->count() === 1) {
                        return [$taskGroups->first()->id];
                    }

                    return null;
                }
            )
            ->afterStateUpdated(function (
                TaskBatchLookupScopeService $lookupService,
                Get $get,
                Set $set
            ) {
                $default = [];
                $taskItemGroups = $lookupService
                    ->taskItemGroups()
                    ->filter(
                        fn($group) => $group->task_group_id === $get(TaskBatchForm::COL_NAME_TG_PICKER)
                    );

                if ($taskItemGroups->count() === 1) {
                    $default = [$taskItemGroups->first()->id];
                }

                $set(TaskBatchForm::COL_NAME_TIG_PICKER, $default);
            });
    }
}
