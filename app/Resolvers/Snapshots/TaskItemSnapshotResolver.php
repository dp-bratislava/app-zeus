<?php

namespace App\Resolvers\Snapshots;

class TaskItemSnapshotResolver
{
    public function __construct(
        protected TaskAssignedToResolver $taAssignedToResolver,
        protected TaskItemAssignedToResolver $tiaAssignedToResolver,
    ) {}

    public function resolve($taskItemAssignment, $taskAssignment): array
    {
      //  dd($taskAssignment);
        return [
            'task_item_id' => $taskItemAssignment->taskItem->id,
            // task
            // 'task_id' => $taskAssignment->task->id,
            // 'task_date' => $taskAssignment->task->date,
            // 'task_title' => $taskAssignment->task->title,
            // 'task_description' => $taskAssignment->task->description,
            // 'task_group_title' => $taskAssignment->task->group->title,
            // 'task_assigned_to' => $this->taAssignedToResolver->resolve($taskAssignment->assignedTo),
            // 'task_author_lastname' => $taskAssignment->author->lastname,
            // 'task_place_of_origin' => $taskAssignment->task->placeOfOrigin->title,
            // 'task_created_at' => $taskAssignment->task->created_at,

            // task item 
            // 'task_item_date' => $taskItemAssignment->taskItem->date,
            // 'task_item_title' => $taskItemAssignment->taskItem->title,
            // 'task_item_description' => $taskItemAssignment->taskItem->description,
            // 'task_item_group_title' => $taskItemAssignment->taskItem->group->title,
            // 'task_item_assigned_to' => $this->tiaAssignedToResolver->resolve($taskItemAssignment->assignedTo),
            // 'task_item_author_lastname' => $taskItemAssignment->author->lastname,
            // 'task_item_created_at' => $taskItemAssignment->taskItem->created_at,

            'updated_at' => now(),
        ];
    }

    public function batchResolve($taskItemAssignments, $taskAssignments) 
    {
        $result = [];

        if (empty($taskItemAssignments)) {
            return $result; 
        }

        foreach ($taskItemAssignments as $tia) {
            $result[] = $this->resolve($tia, $taskAssignments[$tia->taskItem->task->id]);  
        }

        return $result; 
    }
}
