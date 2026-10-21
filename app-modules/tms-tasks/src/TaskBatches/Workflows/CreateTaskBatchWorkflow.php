<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use App\Enums\BatchContext;
use Dpb\Modules\Tasks\TaskBatches\Commands\AssignWorkToTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskAssignmentsCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskItemsCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\CreateTaskBatchWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchConfigMapper;
use Dpb\Package\Batchable\Models\Batch;
use Dpb\Package\Batchable\Resolvers\BatchContextResolver;
use Dpb\Package\Tasks\Models\TaskItem;
use Exception;
use Illuminate\Support\Facades\DB;


class CreateTaskBatchWorkflow
{
    public function __construct(
        private TaskBatchConfigMapper $configMapper,
        private CreateTaskAssignmentsWorkflow $taCWorkflow,
        private CreateTaskItemsWorkflow $tiCWorkflow,
        private AssignWorkToTaskBatchWorkflow $awWorkflow
    ) {}

    public function execute(
        CreateTaskBatchCommand $command,
    ): CreateTaskBatchWorkflowResult {
        try {
            return DB::transaction(function () use ($command) {
                // handle batch
                $tbCWfResult = $this->createBatch($command);

                // task assignemnts
                $taCWfResult = $this->taCWorkflow
                    ->execute(new CreateTaskAssignmentsCommand(
                        date: $command->date,
                        taskConfigs: $this->configMapper->map($command->configuration),
                        context: $command->context
                    ));

                // task items                
                $tiCWfResult = $this->tiCWorkflow
                    ->execute(
                        new CreateTaskItemsCommand(
                            date: $command->date,
                            taskIdMap: $taCWfResult->taskIdMap,
                            taskConfigs: $taCWfResult->taskConfigs,
                            context: $command->context,
                        )
                    );

                // work
                $awWfResult = $this->awWorkflow
                    ->execute(new AssignWorkToTaskBatchCommand(
                        batchId: $tbCWfResult->batch->id,
                        taskBatchId: $tbCWfResult->taskBatch->id,
                        date: $command->date,
                        taskItemConfigs: $tiCWfResult->taskItemConfigs,
                        context: $command->context
                    ));

                // attach batch records
                if ($tbCWfResult->batch !== null) {
                    foreach ($taCWfResult->batchRecords as $morphClass => $ids) {
                        $tbCWfResult->batch->attachRecordIds(
                            $morphClass,
                            $ids
                        );
                    }

                    $tbCWfResult->batch->attachRecordIds(
                        (new TaskItem())->getMorphClass(),
                        $tiCWfResult->taskItemIds
                    );
                }

                return $tbCWfResult;
            });
        } catch (Exception $e) {
            dd($e);
        }
    }

    private function createBatch(
        CreateTaskBatchCommand $command
    ): CreateTaskBatchWorkflowResult {
        $batchContextId = BatchContextResolver::get(BatchContext::TaskBatch->value)?->id;

        if ($batchContextId) {
            $batch = Batch::create([
                'context_id' => $batchContextId,
            ]);

            // handle task batch
            $taskBatch = TaskBatch::create([
                'date' => $command->date,
                'batch_id' => $batch->id,
                'task_group_id' => $command->taskGroupId,
                'author_id' => $command->context->authorId,
                'configuration' => $command->configuration,
                'created_at' => $command->context->handledAt,
                'updated_at' => $command->context->handledAt,
            ]);
        }

        return new CreateTaskBatchWorkflowResult(
            $batch,
            $taskBatch
        );
    }
}
