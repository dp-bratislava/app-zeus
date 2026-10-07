<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers\Profile;

use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchRecordScopeProfile;
use Dpb\Modules\Tasks\TaskBatches\Enums\DepartmentGroup;
use Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\RecordScope\GenericProfile;

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
