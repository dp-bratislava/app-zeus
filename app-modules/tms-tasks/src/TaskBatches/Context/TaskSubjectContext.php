<?php

namespace Dpb\Modules\Tasks\TaskBatches\Context;

use Dpb\Package\TaskMS\Models\TaskAssignment;

/**
 * Unified task subject context created 
 * by task subject adapters used for 
 * TaskAssignmetn policy evaluation
 */

class TaskSubjectContext
{
    public function __construct(
        public string $subjectId,
        public string $subjectType,
    ) {}
}