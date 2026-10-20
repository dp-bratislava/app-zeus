<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use Dpb\Modules\Tasks\DTO\EntityIdMap;
use Dpb\Modules\Tasks\Helpers\EntityIdMapper;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskAssignmentsCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\CreateTaskAssignmentsWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskItemsCommand;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Package\TaskMS\States;
use Dpb\Package\Tasks\Models\TaskItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        $taskConfigs = $this->createTasks($command);

        $taskIdMap = $this->idMapper->map(
            Task::class,
            $taskConfigs->keys()
        );

        // task assignments
        $this->createTaskAssignments($command, $taskIdMap, $taskConfigs);

        // task items
        $tiCWfResult = $this->tiCWorkflow->execute(
            new CreateTaskItemsCommand(
                date: $command->date,
                taskIdMap: $taskIdMap,
                taskConfigs: $taskConfigs,
                context: $command->context,
            )
        );

        $taskAssignmentIds = TaskAssignment::query()
            ->whereIn('task_id', $taskIdMap->ids())
            ->pluck('id')
            ->all();

        $batchRecords[app(TaskItem::class)->getMorphClass()] = $tiCWfResult->taskItemIds;
        $batchRecords[app(TaskAssignment::class)->getMorphClass()] = $taskAssignmentIds;
        // array_merge($batchRecords, $tiCWfResult->)

        return new CreateTaskAssignmentsWorkflowResult(
            batchRecords: $batchRecords,
            taskItemConfigs: $tiCWfResult->taskItemConfigs 
        );
    }

    private function createTasks(
        CreateTaskAssignmentsCommand $command,
    ): Collection {
        $tasks = [];
        $taskConfigs = [];
        foreach ($command->taskConfigs as $taskConfig) {
            $taskUuid = Str::uuid7()->toString();

            $tasks[] = [
                'uuid' => $taskUuid,
                'date' => $command->date,
                'group_id' => $taskConfig->taskGroupId,
                'state' => States\Task\Task\Created::$name,
                'description' => $taskConfig->note,
                'place_of_origin_id' => $command->context->placeOfOriginId,
                'created_at' => $command->context->handledAt,
                'updated_at' => $command->context->handledAt,
            ];

            $taskConfigs[$taskUuid] = $taskConfig;
        }

        Task::insert($tasks);

        return collect($taskConfigs);
    }
   private function createTaskAssignments(
        CreateTaskAssignmentsCommand $command,
        EntityIdMap $taskIdMap,
        Collection $taskConfigs
    ) {
        $taskAssignments = [];

        foreach ($taskIdMap->all() as $taskUuid => $taskId) {
            $config = $taskConfigs->get($taskUuid);

            $taskAssignments[] = [
                'task_id' => $taskId,
                'subject_id' => $config->subject->id,
                'subject_type' => $config->subject->type->value,
                'author_id' => $command->context->authorId,
                'created_at' => $command->context->handledAt,
                'updated_at' => $command->context->handledAt,
            ];
        }

        TaskAssignment::insert($taskAssignments);
    }
}
