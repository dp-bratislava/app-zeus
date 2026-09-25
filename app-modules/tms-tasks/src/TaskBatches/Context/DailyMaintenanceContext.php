<?php

namespace Dpb\Modules\Tasks\TaskBatches\Context;

use Dpb\Package\TaskMS\Models\TaskAssignment;
use Illuminate\Foundation\Auth\User;

class DailyMaintenanceContext
{
    public function __construct(
        public int $dmTaskGroupId,
        public User $user, 
        public DepartmentAccessContext $departmentContext,
        public TaskAssignment|null $taskAssignment = null
    ) {}
}
