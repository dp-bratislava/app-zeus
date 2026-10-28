<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations;

use Dpb\Package\Tasks\Models\TaskItemGroup;
use Illuminate\Support\Collection;

final class TaskItemGroupOptions
{
    /**
     * @return array<int, string>
     */
    public function forTaskGroup(int $taskGroupId): array
    {
        return TaskItemGroup::query()
            ->where('task_group_id', $taskGroupId)
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }

    public function labelFor(
        int $taskItemGroupId,
        int $taskGroupId,
    ): string {
        return $this->forTaskGroup($taskGroupId)[$taskItemGroupId]
            ?? (string) $taskItemGroupId;
    }
}
