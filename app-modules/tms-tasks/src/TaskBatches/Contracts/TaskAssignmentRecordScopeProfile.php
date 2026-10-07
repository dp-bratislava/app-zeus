<?php

namespace Dpb\Modules\Tasks\TaskBatches\Contracts;

use Dpb\Modules\Tasks\TaskBatches\Context\TaskAssignmentContext;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tab profile providing scopes for TaskAssignment records
 */
interface TaskAssignmentRecordScopeProfile
{
    public function listPageTabs(TaskAssignmentContext $taContext): array;
    public function taskAssignmentScope(Builder $query, TaskAssignmentContext $taContext): Builder;
    public function taskItemScope(Builder $query, TaskAssignmentContext $taContext): Builder;
}