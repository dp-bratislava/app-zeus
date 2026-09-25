<?php

namespace Dpb\Modules\Tasks\Repositories;

use Dpb\\WorkOrder;
use Illuminate\Support\Collection;

final class TaskItemRepository
{
    public function getWtfActivityRecords(int $taskItemId): Collection
    {
        return WorkOrder::where('tms_task_item_id', '=', $taskItemId)
            ->with('wtfTasks.activityRecords')
            ->get()
            ->flatMap(
                fn ($wo) => $wo->wtfTasks->flatMap->activityRecords
            );
    }
}
