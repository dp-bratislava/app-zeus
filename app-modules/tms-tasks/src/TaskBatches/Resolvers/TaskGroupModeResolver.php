<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers;

use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Illuminate\Support\Facades\DB;

class TaskGroupModeResolver
{
    public function resolve(
        int $taskGroupId,
        int $departmentId
    ): TaskBatchMode {
        $mode = DB::table('tms_task_batch_department_group')
            ->where('task_group_id', $taskGroupId)
            ->where('department_id', $departmentId)
            ->value('mode');

        return TaskBatchMode::tryFrom($mode);
    }
}