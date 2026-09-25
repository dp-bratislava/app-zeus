<?php

namespace Dpb\Modules\Tasks\Contracts;

use Dpb\Modules\Tasks\Context\TaskBatchResourceContext;
use Illuminate\Database\Eloquent\Collection;

interface TaskBatchLookupScopeProfile
{
    public function taskSubjects(TaskBatchResourceContext $context): array;
    public function taskGroups(TaskBatchResourceContext $context): Collection;
    public function taskItemGroups(TaskBatchResourceContext $context): Collection;
    public function maintenanceGroups(TaskBatchResourceContext $context): array;    
    public function vehicleTypes(TaskBatchResourceContext $context): Collection;
}