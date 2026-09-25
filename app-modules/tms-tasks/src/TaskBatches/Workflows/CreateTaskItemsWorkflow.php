<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Modules\Tasks\Commands\CreateTaskItemsCommand;
use Dpb\Package\TaskMS\Models\TaskItemAssignment;
use Dpb\Package\TaskMS\States;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Modules\Tasks\DTO\CreateTaskItemsWorkflowResult;
use Dpb\Modules\Tasks\Helpers\EntityIdMapper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

class CreateTaskItemsWorkflow
{
    public function __construct(
        private EntityIdMapper $idMapper
    ) {}

    public function execute(
        CreateTaskItemsCommand $command
    ): CreateTaskItemsWorkflowResult {
        return DB::transaction(function () use ($command) {
            $taskItemUuids = $this->createTaskItems($command);

            $taskItemIdMap = $this->idMapper->map(
                TaskItem::class,
                $taskItemUuids,
            );

            // task item assignments
            $this->createTaskItemAssignments($command, $taskItemIdMap->ids());

            return new CreateTaskItemsWorkflowResult(
                $taskItemIdMap->ids(),
                []
            );
        });
    }

    private function createTaskItems(
        CreateTaskItemsCommand $command,
    ): Collection {
        $taskItems = [];
        $taskItemUuids = [];

        foreach ($command->taskIds as $taskId) {
            foreach ($command->taskItemGroupIds as $taskItemGroupId) {
                $taskItemUuid = Str::uuid7()->toString();

                $taskItems[] = [
                    'uuid' => $taskItemUuid,
                    'date' => $command->date,
                    'task_id' => $taskId,
                    'state' => States\Task\TaskItem\Created::$name,
                    'group_id' => $taskItemGroupId,
                    'created_at' => $command->context->handledAt,
                    'updated_at' => $command->context->handledAt,
                ];

                $taskItemUuids[] = $taskItemUuid;
            }
        }

        TaskItem::insert($taskItems);

        return collect($taskItemUuids);
    }

    private function createTaskItemAssignments(
        CreateTaskItemsCommand $command,
        array $taskItemIds
    ) {
        $taskItemAssignments = [];

        foreach ($taskItemIds as $taskItemId) {
            $taskItemAssignments[] = [
                'task_item_id' => $taskItemId,
                'author_id' => $command->context->authorId,
                'created_at' => $command->context->handledAt,
                'updated_at' => $command->context->handledAt,
            ];
        }

        TaskItemAssignment::insert($taskItemAssignments);
    }
}
