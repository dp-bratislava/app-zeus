<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers\Profile;

use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchLookupScopeProfile;
use Dpb\Modules\Tasks\TaskBatches\Enums\DepartmentGroup;
use Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\LookupScope\GenericProfile;

class TaskBatchLookupScopeResolver
{
    public function __construct(
        private GenericProfile $genericProfile,
    ) {}

    public function resolve(?DepartmentGroup $departmentGroup = null): TaskBatchLookupScopeProfile
    {
        return match ($departmentGroup) {
            // generic departments
            default => $this->genericProfile
        };
    }
}
