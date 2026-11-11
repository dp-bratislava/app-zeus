<?php

namespace Dpb\Modules\Tasks\TaskBatches\Profiles\TaskBatchResource\Access;

use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchResourceContext;
use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchAccessProfile;
use Dpb\Modules\Tasks\TaskBatches\Models\DepartmentTaskGroupView;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;

/**
 * Profile providing scopes for TaskBatch resource access
 */
class GenericProfile implements TaskBatchAccessProfile
{
    public function __construct(
        private DepartmentService $departmentService
    ) {}

    public function canViewAny(TaskBatchResourceContext $context): bool
    {
        $activeDepartmetnId = $this->departmentService->getActiveDepartment()->id;

        return DepartmentTaskGroupView::query()
            ->where('department_id', $activeDepartmetnId)
            ->exists();
        // return true;
    }

    /**
     * Summary of canView
     * @param TaskBatch $record
     * @param TaskBatchResourceContext $context
     * @return bool
     */
    public function canView(
        TaskBatch $record,
        TaskBatchResourceContext $context
    ): bool {
        return in_array(
            $record->taskGroup->id,
            $context->departmentContext->taskGroupIds
        );
    }

    public function canCreate(TaskBatchResourceContext $context): bool
    {
        $activeDepartmetnId = $this->departmentService->getActiveDepartment()->id;

        return DepartmentTaskGroupView::query()
            ->where('department_id', $activeDepartmetnId)
            ->exists();
    }

    /**
     * Summary of canUpdate
     * @param TaskBatch $record
     * @param TaskBatchResourceContext $context
     * @return bool
     */
    public function canUpdate(
        TaskBatch $record,
        TaskBatchResourceContext $context
    ): bool {
        return in_array(
            $record->taskGroup->id,
            $context->departmentContext->taskGroupIds
        );
    }

    /**
     * Summary of canDelete
     * @param TaskBatch $record
     * @param TaskBatchResourceContext $context
     * @return bool
     */
    public function canDelete(
        TaskBatch $record,
        TaskBatchResourceContext $context
    ): bool {
        return in_array(
            $record->taskGroup->id,
            $context->departmentContext->taskGroupIds
        );
    }
}
