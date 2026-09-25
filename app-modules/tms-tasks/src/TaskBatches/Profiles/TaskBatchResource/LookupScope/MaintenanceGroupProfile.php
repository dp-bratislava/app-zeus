<?php

namespace Dpb\Modules\Tasks\Profiles\TaskBatchResource\LookupScope;

use Dpb\Package\TaskMS\Enums\TaskGroupCode;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Modules\Tasks\Context\TaskBatchResourceContext;
use Illuminate\Database\Eloquent\Collection;

/**
 * Profile providing scopes for form field data pickers
 */
class MaintenanceGroupProfile extends GenericProfile
{
    public function taskGroups(TaskBatchResourceContext $context): Collection
    {
        return TaskGroup::query()
            ->whereIn('code', [
                TaskGroupCode::DAILY_MAINTENANCE->value,
            ])
            ->whereIn('id', $context->departmentContext->taskGroupIds)
            ->get();
    }
}
