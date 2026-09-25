<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Modules\Tasks\Commands\CreateTaskAssignmentsCommand;
use Dpb\Modules\Tasks\DTO\CreateTaskAssignmentsWorkflowResult;
use Dpb\Modules\Tasks\DTO\EntityIdMap;
use Dpb\Modules\Tasks\Helpers\EntityIdMapper;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Dpb\Package\TaskMS\States;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Modules\Tasks\Commands\CreateTaskItemsCommand;

class CreateTaskAssignmentsWorkflow
{
    public function __construct(
        private EntityIdMapper $idMapper,
        private CreateTaskItemsWorkflow $tiCWorkflow
    ) {}

    public function execute(
        CreateTaskAssignmentsCommand $command,
    ): CreateTaskAssignmentsWorkflowResult {
        // tasks
        $taskSubjects = $this->createTasks($command);

        $taskIdMap = $this->idMapper->map(
            Task::class,
            $taskSubjects->keys()
        );

        // task assignments
        $this->createTaskAssignments($command, $taskIdMap, $taskSubjects);

        // task items
        $ticwResult = $this->tiCWorkflow->execute(
            new CreateTaskItemsCommand(
            date: $command->date,
            taskIds: $taskIdMap->ids(),
            taskItemGroupIds: $command->taskItemGroupIds,
            context: $command->context,                
            )
        );

        $taskAssignmentIds = TaskAssignment::query()
            ->whereIn('task_id', $taskIdMap->ids())
            ->pluck('id')
            ->all();

        $batchRecords[app(TaskItem::class)->getMorphClass()] = $ticwResult->taskItemIds;
        $batchRecords[app(TaskAssignment::class)->getMorphClass()] = $taskAssignmentIds;
        $batchRecords[app(TaskGroup::class)->getMorphClass()] = [$command->taskGroupId];

        return new CreateTaskAssignmentsWorkflowResult(
            batchRecords: $batchRecords
        );
    }

    private function createTasks(
        CreateTaskAssignmentsCommand $command,
    ): Collection {
        $tasks = [];
        $taskSubjects = [];
        foreach ($command->subjects as $subject) {
            $taskUuid = Str::uuid7()->toString();

            $tasks[] = [
                'uuid' => $taskUuid,
                'date' => $command->date,
                'group_id' => $command->taskGroupId,
                'state' => States\Task\Task\Created::$name,
                'place_of_origin_id' => $command->context->placeOfOriginId,
                'created_at' => $command->context->handledAt,
                'updated_at' => $command->context->handledAt,
            ];

            $taskSubjects[$taskUuid] = $subject;
        }

        Task::insert($tasks);

        return collect($taskSubjects);
    }

    private function createTaskAssignments(
        CreateTaskAssignmentsCommand $command,
        EntityIdMap $taskIdMap,
        Collection $taskSubjects
    ) {
        $taskAssignments = [];

        foreach ($taskIdMap->all() as $taskUuid => $taskId) {
            $subject = $taskSubjects->get($taskUuid);

            $taskAssignments[] = [
                'task_id' => $taskId,
                'subject_id' => $subject->id,
                'subject_type' => $subject->type->value,
                'author_id' => $command->context->authorId,
                'created_at' => $command->context->handledAt,
                'updated_at' => $command->context->handledAt,
            ];
        }

        TaskAssignment::insert($taskAssignments);
    }
}
