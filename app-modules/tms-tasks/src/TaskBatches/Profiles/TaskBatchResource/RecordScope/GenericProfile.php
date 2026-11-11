<?php

namespace Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\RecordScope;

use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;
use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchRecordScopeProfile;
use Dpb\Modules\Tasks\TaskBatches\Models\DepartmentTaskGroupView;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tab profile providing scopes for TaskBatch records
 */
class GenericProfile implements TaskBatchRecordScopeProfile
{
    public function taskBatchScope(
        Builder $query,
        TaskBatchResourceContext $context
    ): Builder {
        $taskGroupIds = $context->departmentContext->taskGroupIds;

        return $query
            ->whereIn('task_group_id', $taskGroupIds);
    }

    public function taskGroupScope(
        Builder $query,
        TaskBatchResourceContext $context
    ): Builder {
        $tgIds = DepartmentTaskGroupView::query()
            ->where('department_id', $context->departmentContext->departmentId)
            ->whereIn('task_group_id', $context->departmentContext->taskGroupIds)
            ->pluck('task_group_id as id');

        return $query
            ->whereIn('id', $tgIds);
    }

    public function taskSubjectScope(
        Builder $query,
        TaskBatchResourceContext $context
    ): Builder {
        return $query;
    }
}
