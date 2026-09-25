<?php

namespace Dpb\Modules\Tasks\Contracts;

use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Modules\Tasks\Context\TaskAssignmentContext;

interface TaskAssignmentAccessProfile
{
    public function canViewAny(TaskAssignmentContext $context): bool;
    public function canView(TaskAssignmentContext $context): bool;
    public function canCreate(TaskAssignmentContext $context): bool;
    public function canUpdate(TaskAssignmentContext $context): bool;
    public function canDelete(TaskAssignmentContext $context): bool;

    public function canDeleteTaskItem(TaskAssignmentContext $context): bool;
    public function canEditTaskItem(TaskAssignmentContext $context): bool;
    public function canViewTaskItem(TaskAssignmentContext $context): bool;
    public function canCreateTaskItem(TaskAssignmentContext $context): bool;
    public function canAssignWorkToTaskItem(TaskItem $taskItem, TaskAssignmentContext $context): bool;
}