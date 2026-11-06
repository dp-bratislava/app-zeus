<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers;

use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Modules\Tasks\TaskBatches\Exceptions\InvalidTaskBatchModeException;
use Dpb\Modules\Tasks\TaskBatches\Exceptions\TaskGroupModeNotFoundException;
use Dpb\Modules\Tasks\TaskBatches\Models\DepartmentTaskGroupView;

class TaskGroupModeResolver
{
    public function resolve(
        int $taskGroupId,
        int $departmentId
    ): ?TaskBatchMode {
        $mode = DepartmentTaskGroupView::query()
            ->where('task_group_id', $taskGroupId)
            ->where('department_id', $departmentId)
            ->value('mode');

        if ($mode === null) {
            throw TaskGroupModeNotFoundException::for(
                $taskGroupId,
                $departmentId,
            );
        }

        return TaskBatchMode::tryFrom($mode)
            ?? throw new InvalidTaskBatchModeException($mode);
    }
}
