<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use Dpb\Modules\Tasks\Helpers\EntityIdMapper;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskItemsCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\CreateTaskItemsWorkflowResult;
use Dpb\Package\TaskMS\Models\TaskItemAssignment;
use Dpb\Package\TaskMS\States;
use Dpb\Package\Tasks\Models\TaskItem;
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
            $this->createTaskItemAssignments(
                $command->context,
                $taskItemIdMap->ids()
            );

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
        
        foreach ($command->taskIdMap->all() as $taskUuid => $taskId) {
            $config = $command->configs->get($taskUuid);

            foreach ($config->taskItemGroups as $taskItemGroup) {
                $taskItemUuid = Str::uuid7()->toString();

                $taskItems[] = [
                    'uuid' => $taskItemUuid,
                    'date' => $command->date,
                    'task_id' => $taskId,
                    'state' => States\Task\TaskItem\Created::$name,
                    'group_id' => $taskItemGroup->taskItemGroupId,
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
        TaskBatchHandlerContext $context,
        array $taskItemIds
    ) {
        $taskItemAssignments = [];

        foreach ($taskItemIds as $taskItemId) {
            $taskItemAssignments[] = [
                'task_item_id' => $taskItemId,
                'author_id' => $context->authorId,
                'created_at' => $context->handledAt,
                'updated_at' => $context->handledAt,
            ];
        }

        TaskItemAssignment::insert($taskItemAssignments);
    }
}
