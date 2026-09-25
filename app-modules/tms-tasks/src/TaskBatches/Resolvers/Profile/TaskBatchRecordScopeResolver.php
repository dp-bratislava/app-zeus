<?php

namespace Dpb\Modules\Tasks\Resolvers\Profile;

use Dpb\Modules\Tasks\Contracts\TaskBatchRecordScopeProfile;
use Dpb\Modules\Tasks\Enums\DepartmentGroup;
use Dpb\Modules\Tasks\Profiles\TaskBatchResource\RecordScope\GenericProfile;

class TaskBatchRecordScopeResolver
{
    public function __construct(
        private GenericProfile $genericProfile,
    ) {}

    public function resolve(?DepartmentGroup $departmentGroup = null): TaskBatchRecordScopeProfile
    {
        return match ($departmentGroup) {
            // generic departments
            default => $this->genericProfile
        };
    }
}
