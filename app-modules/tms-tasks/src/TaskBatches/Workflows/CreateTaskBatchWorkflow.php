<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use App\Enums\BatchContext;
use Dpb\Modules\Tasks\TaskBatches\Commands\AssignWorkToTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskAssignmentsCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\CreateTaskBatchWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchConfigMapper;
use Dpb\Package\Batchable\Models\Batch;
use Dpb\Package\Batchable\Resolvers\BatchContextResolver;
use Exception;
use Illuminate\Support\Facades\DB;


class CreateTaskBatchWorkflow
{
    public function __construct(
        private TaskBatchConfigMapper $configMapper,
        private CreateTaskAssignmentsWorkflow $taCWorkflow,
        private AssignWorkToTaskBatchWorkflow $awWorkflow
    ) {}

    public function execute(
        CreateTaskBatchCommand $command,
    ): CreateTaskBatchWorkflowResult {
        try {
            return DB::transaction(function () use ($command) {
                // handle batch
                $tbCWfResult = $this->createBatch($command);

                $taCWfResult = $this->taCWorkflow
                    ->execute(new CreateTaskAssignmentsCommand(
                        date: $command->date,
                        taskConfigs: $this->configMapper->map($command->configuration),
                        context: $command->context
                    ));

                $awWfResult = $this->awWorkflow
                    ->execute(new AssignWorkToTaskBatchCommand(
                        batchId: $tbCWfResult->batch->id,
                        taskBatchId: $tbCWfResult->taskBatch->id,
                        date: $command->date,
                        taskItemConfigs: $taCWfResult->taskItemConfigs,
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
                }

                return $tbCWfResult;
            });
        } catch (Exception $e) {
            dd($e);
        }

        return new CreateTaskBatchWorkflowResult();
    }

    private function createBatch(
        CreateTaskBatchCommand $command
    ): CreateTaskBatchWorkflowResult {
        // handle batch
        // $batchContextId = BatchContextResolver::get(TaskBatchContext::VehicleCleaningB->value)?->id;
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

            $taskBatchConfig = [];
            foreach ($command->configs as $config) {
                foreach ($config->taskItemGroups as $tig) {
                    $taskBatchConfig[] = [
                        'task_batch_id' => $taskBatch->id,
                        'subject_id' => $config->subject->id,
                        'subject_type' => $config->subject->type->value,
                        'task_item_group_id' => $tig->taskItemGroupId,
                        'quantity' => $tig->quantity,
                        'created_at' => $command->context->handledAt,
                        'updated_at' => $command->context->handledAt,
                    ];
                }
            }

            DB::table('tms_task_batch_config')->insert($taskBatchConfig);

            // // 
            // $taskBatchTigs = [];
            // foreach ($command->taskItemGroupIds as $taskItemGroupId) {
            //     $taskBatchTigs[] = [
            //         'task_batch_id' => $taskBatch->id,
            //         'task_item_group_id' => $taskItemGroupId,
            //         'created_at' => $command->context->handledAt,
            //         'updated_at' => $command->context->handledAt,
            //     ];
            // }

            // TaskBatchTaskItemGroup::insert($taskBatchTigs);
        }

        return new CreateTaskBatchWorkflowResult(
            $batch,
            $taskBatch
        );
    }
}
