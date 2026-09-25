<?php

namespace Dpb\Modules\Tasks\Factories;

use Dpb\Modules\Tasks\Context\TaskBatchResourceContext;

class TaskBatchContextFactory
{
    public function __construct(
        private DepartmentContextFactory $departmentContextFactory
    ) {}

    public function make(): TaskBatchResourceContext
    {
        return new TaskBatchResourceContext(
            user: auth()->user(),
            departmentContext: $this->departmentContextFactory->make(),
        );
    }
}