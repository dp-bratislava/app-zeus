<?php

namespace App\Resolvers\Snapshots;

class TaskSubjectResolver
{
    public function resolve($taskItemAssignment): array
    {
        return [
            // 'task_item_id' => $taskItemAssignment->taskItem->id,
            // // task
            // 'task_date' => $taskItemAssignment->taskItem->task->date,
            // 'task_title' => $taskItemAssignment->taskItem->task->title,
            // 'task_description' => $taskItemAssignment->taskItem->task->description,
            // 'task_group_title' => $taskItemAssignment->taskItem->task->group->title,
            // // 'task_maintenance_group' => optional($task->assignments->first()->maintenanceGroup)->title,
            // // 'task_maintenance_group_code' => optional($task->assignments->first()->maintenanceGroup)->title,
            // // 'task_author_lastname' => optional($task->assignments->first()->author)->lastname,
            // 'task_place_of_origin' => $taskItemAssignment->taskItem->task->placeOfOrigin->title,
            // 'task_created_at' => $taskItemAssignment->taskItem->task->created_at,

            // // task item 
            // 'task_item_date' => $taskItemAssignment->taskItem->date,
            // 'task_item_title' => $taskItemAssignment->taskItem->title,
            // 'task_item_description' => $taskItemAssignment->taskItem->description,
            // 'task_item_group_title' => $taskItemAssignment->taskItem->group->title,
            // // 'task_item_maintenance_group' => optional($task->assignments->first()->maintenanceGroup)->title,
            // // 'task_item_maintenance_group_code' => $taskItemAssignment->maintenanceGroup,
            // 'task_item_author_lastname' => $taskItemAssignment->author->lastname,
            // 'task_item_created_at' => $taskItemAssignment->taskItem->created_at,

            // 'updated_at' => now(),
        ];
    }
}
