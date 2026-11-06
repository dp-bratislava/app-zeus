<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations;

use Dpb\Modules\Tasks\Eligibility\Models\DepartmentOperationRuleView;
use Dpb\Modules\Tasks\TaskBatches\Models\DepartmentTaskItemGroupView;

final class TaskItemGroupOptions
{
    /**
     * @return array<int, string>
     */
    public function for(
        int $departmentId,
        int $taskGroupId,
    ): array {
        return DepartmentTaskItemGroupView::query()
            ->where('department_id', $departmentId)
            ->where('task_group_id', $taskGroupId)
            ->get()
            ->mapWithKeys(fn (DepartmentTaskItemGroupView $item) => [
                (int) $item->task_item_group_id => $item->task_item_group_label,
            ])
            ->all();
    }

    public function labelFor(
        int $departmentId,
        int $taskGroupId,
        int $taskItemGroupId,
    ): string {
        return $this->for($departmentId, $taskGroupId)[$taskItemGroupId]
            ?? (string) $taskItemGroupId;
    }
}
