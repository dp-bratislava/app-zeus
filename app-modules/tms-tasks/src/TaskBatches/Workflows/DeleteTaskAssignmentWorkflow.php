<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Package\Tasks\Models\TaskItem;
use Illuminate\Support\Facades\DB;

class DeleteTaskAssignmentWorkflow
{
    public function __construct(
        private DeleteTaskItemWorkflow $tiDWorkflow,
    ) {}

    public function handle(int $taskAssignmentId)
    {
        return DB::transaction(function () use ($taskAssignmentId) {
            $taskItemIds = TaskItem::where('task_id', '=', $taskAssignmentId)
                ->pluck('id');

            foreach ($taskItemIds as $taskItemId) {
                $this->tiDWorkflow->handle($taskItemId);
            }

            $taskAssignment = TaskAssignment::find($taskAssignmentId);
            // delete task
            $taskAssignment->task->delete();

            // delete task assignment
            $taskAssignment->delete();
        });
    }
}
