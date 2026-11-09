<?php

namespace Dpb\Modules\Tasks\TaskBatches\Policies;

use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchAccessService;
use Illuminate\Foundation\Auth\User;

class TaskBatchPolicy
{
    public function __construct(
        private TaskBatchAccessService $accessService
    ) {}

    public function viewAny(User $user): bool
    {
        return $this->accessService
            ->canViewAny();
    }

    public function view(User $user, TaskBatch $taskBatch): bool
    {
        return $this->accessService
            ->canView($taskBatch);
    }

    public function create(User $user): bool
    {
        return $this->accessService
            ->canCreate();
    }

    public function update(User $user, TaskBatch $taskBatch): bool
    {
        return $this->accessService
            ->canUpdate($taskBatch);
    }

    public function delete(User $user, TaskBatch $taskBatch): bool
    {
        return $this->accessService
            ->canDelete($taskBatch);
    }
}
