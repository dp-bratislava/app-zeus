<?php

namespace Dpb\Modules\Tasks\TaskBatches\Context;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class TaskAssignmentContext
{
    public function __construct(
        public User $user, 
        public DepartmentAccessContext $departmentContext,
        public Model|null $subject = null,
        public string|null $taskGroupCode = null,
    ) {}
}
