<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\DTO\EntityIdMap;
use Dpb\Modules\Tasks\Helpers\EntityIdMapper;
use Dpb\Modules\Tasks\TaskBatches\Commands\AssignWorkToTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskItemsCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\ReconcileTaskAssignmentsCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\CreateTaskItemsWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\UpdateTaskBatchWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchConfigMapper;
use Dpb\Modules\Tasks\TaskBatches\Workflows\ReconcileTaskAssignmentsWorkflow;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Package\Tasks\Models\TaskItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UpdateTaskBatchWorkflow
{
    public function __construct(
        private TaskBatchConfigMapper $configMapper,
        private EntityIdMapper $idMapper,
        // private TaskBatchTaskReconciler $taskReconciler,
        private ReconcileTaskAssignmentsWorkflow $taReconcileWorkflow,
        private DeleteTaskItemsWorkflow $tiDWorkflow,
        private CreateTaskItemsWorkflow $tiCWorkflow,
        private AssignWorkToTaskBatchWorkflow $assignWorkWorkflow,
    ) {}

    public function execute(
        UpdateTaskBatchCommand $command
    ): UpdateTaskBatchWorkflowResult {
        return DB::transaction(function () use ($command) {
            $taskConfigs = $this->configMapper->map($command->configuration);

            $taskBatch = TaskBatch::query()
                ->with('batch')
                ->lockForUpdate()
                ->findOrFail($command->taskBatchId);

            // create new tasks and delete unnecessary tasks
            // does not create task items or work
            $taReconcileWfResult = $this->taReconcileWorkflow
                ->execute(
                    new ReconcileTaskAssignmentsCommand(
                        batchId: $taskBatch->batch->id,
                        taskBatchId: $taskBatch->id,
                        date: $command->date,
                        taskConfigs: $taskConfigs,
                        context: $command->context
                    )
                );

            // reconcile batch records
            $batch = $taskBatch->batch;
            if ($batch !== null) {
                foreach ($taReconcileWfResult->recordsToCreate as $morphClass => $ids) {
                    $batch->attachRecordIds(
                        $morphClass,
                        $ids
                    );
                }

                foreach ($taReconcileWfResult->recordsToDelete as $morphClass => $ids) {
                    DB::table('dpb_batchable_batch_records')
                        ->whereLike('record_type', $morphClass)
                        ->whereIn('record_id', $ids)
                        ->where('batch_id', $batch->id)
                        ->delete();
                }
            }

            $taskItemMorphClass = (new TaskItem())->getMorphClass();

            // delete all task items from existing batch tasks
            $this->deleteTaskItems(
                $batch->id,
                $taskItemMorphClass
            );

            // recreate task item data according to new configuration
            $tiCWfResult = $this->createTaskItems(
                $command,
                $taskConfigs,
                $taskItemMorphClass,
                $batch->id
            );

            // recreate work data according to new configuration
            $this->assignWork(
                $taskBatch,
                $command,
                $tiCWfResult->taskItemConfigs
            );

            $this->updateTaskBatch($taskBatch, $command);

            return new UpdateTaskBatchWorkflowResult(
                batch: $taskBatch->batch,
                taskBatch: $taskBatch,
            );
        });
    }

    private function updateTaskBatch(
        TaskBatch $taskBatch,
        UpdateTaskBatchCommand $command,
    ): void {
        $taskBatch->update([
            'date' => $command->date,
            'configuration' => $command->configuration,
            'author_id' => $command->context->authorId,
            'updated_at' => $command->context->handledAt,
        ]);
    }

    private function assignWork(
        TaskBatch $taskBatch,
        UpdateTaskBatchCommand $command,
        Collection $taskItemConfigs
    ) {
        $awWfResult = $this->assignWorkWorkflow
            ->execute(new AssignWorkToTaskBatchCommand(
                batchId: $taskBatch->batch->id,
                taskBatchId: $taskBatch->id,
                date: $command->date,
                taskItemConfigs: $taskItemConfigs,
                context: $command->context
            ));
    }

    private function createTaskItems(
        UpdateTaskBatchCommand $command,
        Collection $taskConfigs,
        string $taskItemMorphClass,
        int $batchId
    ): CreateTaskItemsWorkflowResult {
        $taMorphClass = (new TaskAssignment())->getMorphClass();

        $tasks = DB::table('tsk_tasks as t')
            ->join('tms_task_assignments as ta', 'ta.task_id', '=', 't.id')
            ->join('dpb_batchable_batch_records as br', function ($join) use ($taMorphClass, $batchId) {
                $join->on('br.record_id', '=', 'ta.id')
                    ->where('br.record_type', '=', $taMorphClass)
                    ->where('br.batch_id', '=', $batchId);
            })
            ->get([
                't.id',
                't.uuid',
                'ta.subject_type',
                'ta.subject_id',
            ]);

        $taskConfigsBySubject = $taskConfigs->keyBy(
            fn(TaskBatchTaskConfig $config) =>
            "{$config->subject->type->value}:{$config->subject->id}"
        );

        $tasksByUuid = $tasks->mapWithKeys(
            function ($task) use ($taskConfigsBySubject) {
                $subjectKey = "{$task->subject_type}:{$task->subject_id}";
                $config = $taskConfigsBySubject->get($subjectKey);

                if ($config === null) {
                    return [];
                }

                return [
                    $task->uuid => [
                        'id' => (int) $task->id,
                        'config' => $config,
                    ],
                ];
            }
        );

        $taskIdMap = new EntityIdMap(
            $tasksByUuid->map(fn(array $task) => $task['id'])
        );

        $taskConfigsByUuid = $tasksByUuid->map(
            fn(array $task) => $task['config']
        );

        // map task configs to tasks

        $tiCWfResult = $this->tiCWorkflow
            ->execute(new CreateTaskItemsCommand(
                date: $command->date,
                taskIdMap: $taskIdMap,
                taskConfigs: $taskConfigsByUuid,
                context: $command->context,
            ));

        $taskItems = collect($tiCWfResult->taskItemIds)
            ->map(fn($taskItemId) => [
                'record_type' => $taskItemMorphClass,
                'record_id' => $taskItemId,
                'batch_id' => $batchId,
            ]);

        DB::table('dpb_batchable_batch_records')
            ->insert($taskItems->toArray());

        return $tiCWfResult;
    }

    private function deleteTaskItems(
        int $batchId,
        string $taskItemMorphClass
    ): void {
        $taskItems = DB::table('dpb_batchable_batch_records')
            ->where('record_type', '=', $taskItemMorphClass)
            ->where('batch_id', '=', $batchId)
            ->pluck('record_id', 'id');

        // dd(
        //     $taskItems->all(),
        //     $taskItems->keys()->all()
        // );
        $this->tiDWorkflow
            ->execute($taskItems->all());

        DB::table('dpb_batchable_batch_records')
            ->whereIn('id', $taskItems->keys()->all())
            ->delete();
    }
}
