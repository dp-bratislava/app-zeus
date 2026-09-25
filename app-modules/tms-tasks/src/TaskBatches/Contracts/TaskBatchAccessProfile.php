<?php

namespace Dpb\Modules\Tasks\Contracts;

use Dpb\Modules\Tasks\Context\TaskBatchResourceContext;

interface TaskBatchAccessProfile
{
    public function canViewAny(TaskBatchResourceContext $context): bool;
    public function canView(TaskBatchResourceContext $context): bool;
    public function canCreate(TaskBatchResourceContext $context): bool;
    public function canUpdate(TaskBatchResourceContext $context): bool;
    public function canDelete(TaskBatchResourceContext $context): bool;

    // public function canDeleteTaskItem(TaskBatchContext $context): bool;
    // public function canEditTaskItem(TaskBatchContext $context): bool;
    // public function canViewTaskItem(TaskBatchContext $context): bool;
    // public function canCreateTaskItem(TaskBatchContext $context): bool;
    // public function canAssignWorkToTaskItem(TaskItem $taskItem, TaskBatchContext $context): bool;
}