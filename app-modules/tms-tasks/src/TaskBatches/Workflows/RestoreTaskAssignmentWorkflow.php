<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\TaskMS\Handlers\Task\UpdateTaskHandler;
use Dpb\Package\TaskMS\Handlers\TaskAssignment\UpdateTaskAssignmentHandler;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\TaskMS\Models\TaskItemAssignment;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Package\Tasks\Models\TaskItem;
use Illuminate\Support\Facades\DB;

class RestoreTaskAssignmentWorkflow
{
    public function __construct(
        private UpdateTaskHandler $taskCHdl,
        private UpdateTaskAssignmentHandler $taskAssignmentCHdl,
    ) {}

    // public function handle(int $taskAssignmentId)
    public function handle(TaskAssignment $taskAssignment)
    {
        // dd('delete WF');
        return DB::transaction(function () use ($taskAssignment) {
            $taskId = $taskAssignment->task_id;
            $taskItemIds = TaskItem::withTrashed()
                ->where('task_id', '=', $taskId)
                ->pluck('id');

            // restore activities
            // restore wtf tasks
            // restore wtf work orders
            // restore task item assignments
            TaskItemAssignment::withTrashed()
                ->whereIn('task_item_id', $taskItemIds)
                ->restore();

            // restore task items
            TaskItem::withTrashed()
                ->whereIn('id', $taskItemIds)
                ->restore();

            // restore task
            Task::withTrashed()
                ->find($taskId)
                ->restore();

            // restore task assignment
            $taskAssignment->restore();
        });
    }
}
