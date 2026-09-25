<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Modules\Tasks\Commands\CreateTaskItemsCommand;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Modules\Tasks\Commands\ReconcileTaskItemsCommand;
use Dpb\Modules\Tasks\DTO\ReconcileTaskItemsWorkflowResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReconcileTaskItemsWorkflow
{
    public function __construct(
        private CreateTaskItemsWorkflow $createTaskItemsWorkflow,
        private DeleteTaskItemsWorkflow $deleteTaskItemsWorkflow,
    ) {}

    public function execute(
        ReconcileTaskItemsCommand $command
    ): ReconcileTaskItemsWorkflowResult {
        $originalBatchTaskItems = $this->getExistingTaskItems(
            $command->batchId
        );

        $batchTaskItemsByKey = $originalBatchTaskItems->keyBy(
            fn($item) => "{$item->task_id}:{$item->group_id}"
        );

        $desiredTaskItemsByKey = collect($command->taskIds)
            ->crossJoin($command->taskItemGroupIds)
            ->mapWithKeys(
                fn(array $pair) => [
                    "{$pair[0]}:{$pair[1]}" => [
                        'task_id' => $pair[0],
                        'group_id' => $pair[1],
                    ],
                ]
            );

        $taskItemsToCreate = $desiredTaskItemsByKey->diffKeys(
            $batchTaskItemsByKey
        );

        $taskItemGroupIdsToCreate = $taskItemsToCreate
            ->pluck('group_id')
            ->unique()
            ->values()
            ->all();

        $taskItemsToDelete = $batchTaskItemsByKey->diffKeys(
            $desiredTaskItemsByKey
        );

        $taskItemsToKeep = $batchTaskItemsByKey->intersectByKeys(
            $desiredTaskItemsByKey
        );

        // create
        $ctiwResult = $this->createTaskItemsWorkflow->execute(
            new CreateTaskItemsCommand(
                date: $command->date,
                taskIds: $command->taskIds,
                taskItemGroupIds: $taskItemGroupIdsToCreate,
                context: $command->context
            )
        );

        // delete
        $this->deleteTaskItemsWorkflow->handle(
            $taskItemsToDelete->pluck('id')->all()
        );

        // update
        $this->updateTaskItems($taskItemsToKeep, $command);

        $recordsToCreate[app(TaskItem::class)->getMorphClass()] = $ctiwResult->taskItemIds;
        $recordsToDelete[app(TaskItem::class)->getMorphClass()] = $taskItemsToDelete->pluck('id')->all();

        return new ReconcileTaskItemsWorkflowResult(
            recordsToCreate: $recordsToCreate,
            recordsToDelete: $recordsToDelete,
        );
    }

    private function getExistingTaskItems(
        int $batchId,
    ): Collection {
        $taskItemMorph = app(TaskItem::class)->getMorphClass();

        return DB::table('dpb_batchable_batch_records as br')
            ->join(
                'tsk_task_items as ti',
                'ti.id',
                '=',
                'br.record_id'
            )
            ->where('br.batch_id', $batchId)
            ->where('br.record_type', $taskItemMorph)
            ->whereNull('ti.deleted_at')
            ->get([
                'ti.id',
                'ti.task_id',
                'ti.group_id',
            ]);
    }

    private function updateTaskItems(
        Collection $taskItemsToKeep,
        ReconcileTaskItemsCommand $command,
    ): void {
        // dd($toKeep);
        TaskItem::query()
            ->whereIn('id', $taskItemsToKeep->pluck('id')->all())
            ->update([
                'date' => $command->date,
                'updated_at' => $command->context->handledAt,
            ]);
    }
}
