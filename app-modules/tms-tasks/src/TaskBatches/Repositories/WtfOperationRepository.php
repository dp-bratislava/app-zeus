<?php

namespace Dpb\Modules\Tasks\TaskBatches\Repositories;

use Dpb\WtfTmsBridge\Models\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class WtfOperationRepository
{
    public function getAvailableOperations(
        int $taskItemGroupId,
        Model $taskSubject
    ): Collection {
        $vehicleModel = $taskSubject->model;

        return WorkOrder::where('tms_task_item_id', '=', $taskItemId)
            ->with('wtfTasks.activityRecords')
            ->get()
            ->flatMap(
                fn($wo) => $wo->wtfTasks->flatMap->activityRecords
            );
    }
}
