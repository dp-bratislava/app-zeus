<?php

namespace Dpb\Modules\Tasks\Resolvers\Profile;

use Dpb\Modules\Tasks\Contracts\TaskBatchLookupScopeProfile;
use Dpb\Modules\Tasks\Enums\DepartmentGroup;
use Dpb\Modules\Tasks\Profiles\TaskBatchResource\LookupScope\GenericProfile;
use Dpb\Modules\Tasks\Profiles\TaskBatchResource\LookupScope\MaintenanceGroupProfile;

class TaskBatchLookupScopeResolver
{
    public function __construct(
        private GenericProfile $genericProfile,
        private MaintenanceGroupProfile $mgProfile,
    ) {}

    public function resolve(?DepartmentGroup $departmentGroup = null): TaskBatchLookupScopeProfile
    {
        return match ($departmentGroup) {
            DepartmentGroup::MaintenanceGroup => $this->mgProfile,

            // generic departments
            default => $this->genericProfile
        };
    }
}
