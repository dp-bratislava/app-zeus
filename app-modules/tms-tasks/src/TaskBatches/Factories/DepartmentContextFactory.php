<?php

namespace Dpb\Modules\Tasks\TaskBatches\Factories;

use Dpb\Departments\Services\DepartmentService;
use Dpb\Package\TaskMS\Repositories\DepartmentAssignmentRepository;
use Dpb\Modules\Tasks\TaskBatches\Context\DepartmentAccessContext;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\DepartmentGroupResolver;

/**
 * Custom access permission rules for TaskAssignment TaskGroups
 */
class DepartmentContextFactory
{
    // per request cache (static property saves value to class definition in memory)
    private static ?DepartmentAccessContext $memoized = null;

    public function __construct(
        private DepartmentService $departmentService,
        private DepartmentAssignmentRepository $daRepo,
        private DepartmentGroupResolver $departmentGroupResolver
    ) {}

    public function make(): DepartmentAccessContext
    {
        if (static::$memoized !== null) {
            return static::$memoized;
        }
        return static::$memoized = new DepartmentAccessContext(
            departmentId: $this->departmentService->getActiveDepartment()->id,
            taskGroupIds: $this->daRepo->getTaskGroupIdsByActiveDepartment(),
            vehicleTypeIds: $this->daRepo->getVehicleTypeIdsByActiveDepartment(),
            maintenanceGroupIds: $this->daRepo->getMaintenanceGroupIdsByActiveDepartment(),
            taskItemGroupIds: $this->daRepo->getTaskItemGroupIdsByActiveDepartment(),
            departmentGroup: $this->departmentGroupResolver->resolve(),
        );
    }
}
