<?php

namespace Dpb\Modules\Tasks\Repositories;

use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\\WorkOrder;
use Illuminate\Support\Collection;

final class TaskAssignmentRepository
{
    public function getWtfActivityRecords(int $taskAssignmentId): Collection
    {
        $taskId = TaskAssignment::find($taskAssignmentId)->task->id;
        $taskItemIds = TaskItem::where('task_id', '=', $taskId)
            ->pluck('id');

        return WorkOrder::whereIn('tms_task_item_id', $taskItemIds)
            ->with('wtfTasks.activityRecords')
            ->get()
            ->flatMap(
                fn ($wo) => $wo->wtfTasks->flatMap->activityRecords
            );
    }
}
