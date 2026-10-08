<?php

namespace Dpb\Modules\Tasks\TaskBatches\Context;

use Dpb\Modules\Tasks\TaskBatches\Enums\DepartmentGroup;

class DepartmentAccessContext
{
    public function __construct(
        public readonly int $departmentId,
        public readonly array $taskGroupIds,
        public readonly array $vehicleTypeIds,
        public readonly array $maintenanceGroupIds,
        public readonly array $taskItemGroupIds,
        public readonly DepartmentGroup|null $departmentGroup = null
    ) {}
}