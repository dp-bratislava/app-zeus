<?php

namespace Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\RecordScope;

use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;
use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchRecordScopeProfile;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tab profile providing scopes for TaskBatch records
 */
class GenericProfile implements TaskBatchRecordScopeProfile
{
    public function taskBatchScope(Builder $query, TaskBatchResourceContext $context): Builder
    {
        $taskGroupIds = $context->departmentContext->taskGroupIds;

        return $query
            ->whereIn('task_group_id', $taskGroupIds);
    }
}
