<?php

namespace App\Services\Snapshots;

use App\Resolvers\Snapshots\TaskItemResolver;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\TaskMS\Models\TaskItemAssignment;
use Illuminate\Support\Facades\DB;

class TaskItemSnapshotService
{
    public function __construct(
        public array $taskItemIds
    ) {}

    public function handle(): void
    {
        $baseQuery = TaskItemAssignment::whereIn('task_item_id', $this->taskItemIds);

        // Step 1: lightweight ID extraction
        // $taskIds = (clone $baseQuery)
        //     ->join('tsk_task_items', 'task_items.id', '=', 'tms_task_item_assignments.task_item_id')
        //     ->pluck('tsk_task_items.task_id')
        //     ->unique();

        $tias = $baseQuery
            ->with([
                'taskItem',
                'taskItem.group',
                'taskItem.task',
                'taskItem.task.group',
                'taskItem.task.placeOfOrigin',
                'author:id,lastname',
                // 'assignments.maintenanceGroup',
            ])
            ->get()
            ->keyBy('id');

        // $tas = TaskAssignment::whereIn('task_id', $taskIds)
        //     ->with([
        //         'task',
        //         'task.group',
        //         'task.placeOfOrigin',
        //         'author:id,lastname',
        //         // 'assignments.maintenanceGroup',
        //     ])
        //     ->get()
        //     ->keyBy('id');

        $taskItemResolver = app(TaskItemResolver::class);
        $taskSnapshots = $taskItemResolver->batchResolve($tias);

        // dd($taskSnapshots);
        DB::table('mvw_task_item_snapshots')->upsert(
            $taskSnapshots,
            ['task_id'],
            // ['task_item_date', 'task_item_title', 'create_at', 'updated_at']
        );
    }
}
