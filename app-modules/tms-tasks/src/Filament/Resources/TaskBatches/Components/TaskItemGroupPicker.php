<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class TaskItemGroupPicker
{
    public static function make(?string $name = TaskBatchForm::COL_NAME_TIG_PICKER): Component
    {
        return ToggleButtons::make($name)
            ->label(__('dpb-mod-tasks::task-batch.form.fields.task_item_groups'))
            ->inline()
            ->options(
                fn(
                    TaskBatchLookupScopeService $lookupService,
                    Get $get
                ): array => $lookupService
                    ->taskItemGroups()
                    ->filter(
                        fn($group) => $group->task_group_id === $get(TaskBatchForm::COL_NAME_TG_PICKER)
                    )
                    ->pluck('title', 'id')
                    ->toArray()
            )
            ->default(
                function (
                    TaskBatchLookupScopeService $lookupService,
                ): ?array {
                    $taskItemGroups = $lookupService->taskItemGroups();

                    if ($taskItemGroups->count() === 1) {
                        return [$taskItemGroups->first()->id];
                    }

                    return null;
                }
            )
            ->multiple()
            // ->live()
            ->required();
    }
}
