<?php

namespace Dpb\Modules\Tasks\Factories;

use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Modules\Tasks\Context\TaskAssignmentContext;

class TaskAssignmentContextFactory
{
    public function __construct(
        private DepartmentContextFactory $departmentContextFactory
    ) {}

    public function make(TaskAssignment|null $taskAssignment = null): TaskAssignmentContext
    {
        return new TaskAssignmentContext(
            auth()->user(),
            $this->departmentContextFactory->make(),
            $taskAssignment?->subject,
            $taskAssignment?->task?->group?->code,
        );
    }
}