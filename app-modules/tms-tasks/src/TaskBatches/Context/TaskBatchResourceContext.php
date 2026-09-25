<?php

namespace Dpb\Modules\Tasks\TaskBatches\Context;

use Illuminate\Foundation\Auth\User;

class TaskBatchResourceContext
{
    public function __construct(
        public User $user, 
        public DepartmentAccessContext $departmentContext,
    ) {}
}
