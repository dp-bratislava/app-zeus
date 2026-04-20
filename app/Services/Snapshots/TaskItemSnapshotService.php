<?php

namespace App\Services\Snapshots;

use Dpb\Package\TaskMS\Models\TaskItemAssignment;
use Illuminate\Support\Facades\DB;

class TaskItemSnapshotService
{
    public function __construct(
        public array $taskItemIds
    ) {}

    public function handle(): void
    {
        $tias = TaskItemAssignment::whereIn('task_item_id', $this->taskItemIds)
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

        $taskSnapshots = [];

        foreach ($tias as $tia) {
            $taskSnapshots[] = [
                'task_item_id' => $tia->taskItem->id,
                // task
                'task_date' => $tia->taskItem->task->date,
                'task_title' => $tia->taskItem->task->title,
                'task_description' => $tia->taskItem->task->description,
                'task_group_title' => $tia->taskItem->task->group->title,
                // 'task_maintenance_group' => optional($task->assignments->first()->maintenanceGroup)->title,
                // 'task_maintenance_group_code' => optional($task->assignments->first()->maintenanceGroup)->title,
                // 'task_author_lastname' => optional($task->assignments->first()->author)->lastname,
                'task_place_of_origin' => $tia->taskItem->task->placeOfOrigin->title,
                'task_created_at' => $tia->taskItem->task->created_at,

                // task item 
                'task_item_date' => $tia->taskItem->date,
                'task_item_title' => $tia->taskItem->title,
                'task_item_description' => $tia->taskItem->description,
                'task_item_group_title' => $tia->taskItem->group->title,
                // 'task_item_maintenance_group' => optional($task->assignments->first()->maintenanceGroup)->title,
                // 'task_item_maintenance_group_code' => $tia->maintenanceGroup,
                'task_item_author_lastname' => $tia->author->lastname,
                'task_item_created_at' => $tia->taskItem->created_at,

                'updated_at' => now(),
            ];
        }
// dd($taskSnapshots);
        DB::table('mvw_task_item_snapshots')->upsert(
            $taskSnapshots,
            ['task_id'],
            // ['task_item_date', 'task_item_title', 'create_at', 'updated_at']
        );
    }
}
