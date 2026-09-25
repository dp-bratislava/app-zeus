<?php

namespace Dpb\Modules\Tasks\Contracts;

use Dpb\Modules\Tasks\Context\TaskAssignmentContext;

interface TaskAssignmentLookupScopeProfile
{
    public function taskSubjects(TaskAssignmentContext $taContext): array;
    public function taskGroups(TaskAssignmentContext $taContext): array;
    public function taskItemGroups(TaskAssignmentContext $taContext): array;
    public function maintenanceGroups(TaskAssignmentContext $taContext): array;    
}