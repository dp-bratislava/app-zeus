<?php

namespace Dpb\Modules\Tasks\TaskBatches\Contracts;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;
use Illuminate\Support\Collection;

interface TaskBatchLookupScopeProfile
{
    public function employeeContracts(TaskBatchResourceContext $context, CarbonImmutable $date): Collection;
    public function taskSubjects(TaskBatchResourceContext $context): Collection;
    public function taskGroups(TaskBatchResourceContext $context): Collection;
    public function taskItemGroups(TaskBatchResourceContext $context): Collection;
    public function maintenanceGroups(TaskBatchResourceContext $context): Collection;
    public function vehicleTypes(TaskBatchResourceContext $context): Collection;
}
