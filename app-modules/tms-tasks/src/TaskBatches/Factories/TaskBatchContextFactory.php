<?php

namespace Dpb\Modules\Tasks\TaskBatches\Factories;

use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;

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