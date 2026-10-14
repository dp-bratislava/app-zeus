<?php

namespace Dpb\Modules\Tasks\TaskBatches\Contracts;

use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface TaskBatchLookupScopeProfile
{
    public function employeeContracts(TaskBatchResourceContext $context): array;
    public function taskSubjects(TaskBatchResourceContext $context): array;
    public function taskGroups(TaskBatchResourceContext $context): Collection;
    public function taskItemGroups(TaskBatchResourceContext $context): Collection;
    public function batchableTaskItemGroups(TaskBatchResourceContext $context): SupportCollection;
    public function maintenanceGroups(TaskBatchResourceContext $context): array;    
    public function vehicleTypes(TaskBatchResourceContext $context): Collection;
}