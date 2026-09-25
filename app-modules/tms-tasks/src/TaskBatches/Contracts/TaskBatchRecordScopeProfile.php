<?php

namespace Dpb\Modules\Tasks\Contracts;

use Dpb\Modules\Tasks\Context\TaskBatchResourceContext;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tab profile providing scopes for TaskBatch records
 */
interface TaskBatchRecordScopeProfile
{
    public function taskBatchScope(Builder $query, TaskBatchResourceContext $taContext): Builder;
}