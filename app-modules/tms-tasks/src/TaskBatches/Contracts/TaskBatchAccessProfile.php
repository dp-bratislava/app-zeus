<?php

namespace Dpb\Modules\Tasks\TaskBatches\Contracts;

use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;

interface TaskBatchAccessProfile
{
    public function canViewAny(TaskBatchResourceContext $context): bool;

    /**
     * Summary of canView
     * @param TaskBatch $record
     * @param TaskBatchResourceContext $context
     * @return bool
     */
    public function canView(
        TaskBatch $record,
        TaskBatchResourceContext $context
    ): bool;

    public function canCreate(TaskBatchResourceContext $context): bool;

    /**
     * Summary of canUpdate
     * @param TaskBatch $record
     * @param TaskBatchResourceContext $context
     * @return bool
     */
    public function canUpdate(
        TaskBatch $record,
        TaskBatchResourceContext $context
    ): bool;

    /**
     * Summary of canDelete
     * @param TaskBatch $record
     * @param TaskBatchResourceContext $context
     * @return bool
     */
    public function canDelete(
        TaskBatch $record,
        TaskBatchResourceContext $context
    ): bool;

    // public function canDeleteTaskItem(TaskBatchContext $context): bool;
    // public function canEditTaskItem(TaskBatchContext $context): bool;
    // public function canViewTaskItem(TaskBatchContext $context): bool;
    // public function canCreateTaskItem(TaskBatchContext $context): bool;
    // public function canAssignWorkToTaskItem(TaskItem $taskItem, TaskBatchContext $context): bool;
}
