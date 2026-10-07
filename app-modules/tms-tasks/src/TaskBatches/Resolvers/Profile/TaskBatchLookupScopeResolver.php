<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers\Profile;

use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchLookupScopeProfile;
use Dpb\Modules\Tasks\TaskBatches\Enums\DepartmentGroup;
use Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\LookupScope\GenericProfile;
use Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\LookupScope\MaintenanceGroupProfile;

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
