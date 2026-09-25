<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Modules\Tasks\DTO\DeleteTaskAssignmentsWorkflowResult;
use Illuminate\Support\Facades\DB;

class DeleteTaskAssignmentsWorkflow
{
    public function __construct(
        private DeleteTaskItemsWorkflow $tiDWorkflow,
    ) {}

    public function execute(
        array $taskAssignmentIds
    ): DeleteTaskAssignmentsWorkflowResult {
        $batchRecords = [];

        DB::transaction(function () use ($taskAssignmentIds) {
            $taskIds = TaskAssignment::whereIn('id', $taskAssignmentIds)
                ->pluck('task_id')
                ->toArray();

            $taskItemIds = TaskItem::whereIn('task_id', $taskIds)
                ->pluck('id')
                ->toArray();

            // delete task items
            $this->tiDWorkflow->handle($taskItemIds);

            // delete tasks
            Task::whereIn('id', $taskIds)
                ->delete();

            // delete task assignments
            TaskAssignment::whereIn('id', $taskAssignmentIds)
                ->delete();

            $batchRecords[app(TaskItem::class)->getMorphClass()] = $taskItemIds;
            $batchRecords[app(TaskAssignment::class)->getMorphClass()] = $taskAssignmentIds;
        });

        return new DeleteTaskAssignmentsWorkflowResult(
            batchRecords: $batchRecords
        );
    }
}
