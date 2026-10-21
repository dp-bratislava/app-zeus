<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use Dpb\Modules\Tasks\Enums\TaskItemStatus as EnumTaskItemStatus;
use Dpb\Modules\Tasks\Helpers\EntityIdMapper;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskItemsCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\CreateTaskItemsWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\DTO\WtfTaskItemConfig;
use Dpb\Package\TaskMS\Models\TaskItemAssignment;
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
            $taskItemConfigs = $this->createTaskItems($command);

            $taskItemIdMap = $this->idMapper->map(
                TaskItem::class,
                $taskItemConfigs->keys(),
            );

            // task item assignments
            $this->createTaskItemAssignments(
                $command->context,
                $taskItemIdMap->ids()
            );

            return new CreateTaskItemsWorkflowResult(
                taskItemIds: $taskItemIdMap->ids(),
                taskItemAssignmentIds: [],
                taskItemConfigs: $taskItemConfigs
            );
        });
    }

    private function createTaskItems(
        CreateTaskItemsCommand $command,
    ): Collection {
        $taskItems = [];
        $pendingConfigs = [];

        // dd($command);

        foreach ($command->taskIdMap->all() as $taskUuid => $taskId) {
            $taskConfig = $command->taskConfigs->get($taskUuid);

            if ($taskConfig === null) {
                throw new \LogicException(
                    "Missing task config for task UUID {$taskUuid}."
                );
            }

            foreach ($taskConfig->tigConfigs as $tigConfig) {
                $taskItemUuid = Str::uuid7()->toString();

                $taskItems[] = [
                    'uuid' => $taskItemUuid,
                    'date' => $command->date,
                    'task_id' => $taskId,
                    'state' => EnumTaskItemStatus::Closed->value,
                    'group_id' => $tigConfig->taskItemGroupId,
                    'created_at' => $command->context->handledAt,
                    'updated_at' => $command->context->handledAt,
                ];

                // Keep the data needed to build the final DTO.
                $pendingConfigs[$taskItemUuid] = [
                    'subject' => $taskConfig->subject,
                    'taskItemGroupId' => $tigConfig->taskItemGroupId,
                    'work' => $tigConfig->work,
                ];
            }
        }
        if ($taskItems === []) {
            return collect();
        }

        TaskItem::insert($taskItems);

        // One query to retrieve generated database IDs.
        $taskItemIds = TaskItem::query()
            ->whereIn('uuid', array_keys($pendingConfigs))
            ->pluck('id', 'uuid');

        // Build the final DTOs keyed by task item UUID.
        $taskItemConfigs = [];

        foreach ($pendingConfigs as $uuid => $config) {
            $taskItemId = $taskItemIds->get($uuid);

            if ($taskItemId === null) {
                throw new \RuntimeException(
                    "Could not find inserted task item with UUID {$uuid}."
                );
            }

            $taskItemConfigs[$uuid] = new WtfTaskItemConfig(
                subject: $config['subject'],
                taskItemGroupId: $config['taskItemGroupId'],
                taskItemId: (int) $taskItemId,
                work: $config['work'],
            );
        }

        return collect($taskItemConfigs);
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
