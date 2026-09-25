<?php

namespace Dpb\Modules\Tasks\Resolvers\Profile;

use Dpb\Modules\Tasks\Contracts\TaskBatchAccessProfile;
use Dpb\Modules\Tasks\Enums\DepartmentGroup;
// use Dpb\Modules\Tasks\Profiles\TaskAssignmentResource\Access\AssignWorkOnlyProfile;
use Dpb\Modules\Tasks\Profiles\TaskBatchResource\Access\GenericProfile;

class TaskBatchAccessResolver
{
    public function __construct(
        private GenericProfile $genericProfile,
        // private AssignWorkOnlyProfile $awoProfile,
    ) {}

    public function resolve(?DepartmentGroup $departmentGroup = null): TaskBatchAccessProfile
    {
        return match ($departmentGroup) {
            // gumari
            // DepartmentGroup::TireManagement => $this->awoProfile,
            // vehicle cleaning
            // DepartmentGroup::VehicleCleaning => $this->awoProfile,

            // generic departments
            default => $this->genericProfile
        };
    }
}
