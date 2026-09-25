<?php

namespace Dpb\Modules\Tasks\Profiles\TaskBatchResource\Access;

use Dpb\Modules\Tasks\Context\TaskBatchResourceContext;
use Dpb\Modules\Tasks\Contracts\TaskBatchAccessProfile;

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
