<?php

namespace Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\Access;

use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;
use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchAccessProfile;

/**
 * Tab profile providing scopes for TaskBatch resiurce access
 */
class GenericProfile implements TaskBatchAccessProfile
{
    public function canViewAny(TaskBatchResourceContext $context): bool
    {
        return true;
    }

    public function canView(TaskBatchResourceContext $context): bool
    {
        return true;
    }

    public function canCreate(TaskBatchResourceContext $context): bool
    {
        return true;
    }

    public function canUpdate(TaskBatchResourceContext $context): bool
    {
        return true;
    }

    public function canDelete(TaskBatchResourceContext $context): bool
    {
        return true;
    }
}
