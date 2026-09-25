<?php

namespace Dpb\Modules\Tasks\Repositories;

use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\\WorkOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class WorkOrderRepository
{
    public function __construct(
        private WorkOrder $woModel,
        private DB $dbHdl,
    ) {}

    // public function getActivitiesByTaskAssignment(TaskAssignment $taskAssignment): Collection
    // {
    //     return $this->operationModel
    //         ->whereHas('parent', function ($q) use ($categoryId) {
    //             $q->where('id', '=', $categoryId);
    //         })
    //         ->get();
    // }
}
