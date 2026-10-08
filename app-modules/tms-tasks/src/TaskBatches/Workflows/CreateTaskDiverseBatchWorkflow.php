<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use App\Enums\BatchContext;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskAssignmentsCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskDiverseBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskUniformBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatchTaskItemGroup;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatchTaskSubject;
use Dpb\Package\Batchable\Models\Batch;
use Dpb\Package\Batchable\Resolvers\BatchContextResolver;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskBatchContext;
use Dpb\Package\Tasks\Models\TaskGroup;
use Exception;
use Illuminate\Support\Facades\DB;


class CreateTaskDiverseBatchWorkflow
{
    public function __construct(
        private CreateTaskAssignmentsWorkflow $taCWorkflow
    ) {}

    public function handle(
        CreateTaskDiverseBatchCommand $command,
    ) {
        $result = null;

        try {
            $result = DB::transaction(function () use ($command) {
                // handle batch
                $batch = $this->createBatch($command);

                // $wfResult = $this->taCWorkflow
                //     ->execute(new CreateTaskAssignmentsCommand(
                //         date: $command->date,
                //         taskGroupId: $command->taskGroupId,
                //         subjects: $command->subjects,
                //         taskItemGroupIds: $command->taskItemGroupIds,
                //         context: $command->context,
                //     ));

                // // attach batch records
                // if ($batch !== null) {
                //     foreach ($wfResult->batchRecords as $morphClass => $ids) {
                //         $batch->attachRecordIds(
                //             $morphClass,
                //             $ids
                //         );
                //     }
                // }
            });
        } catch (Exception $e) {
            dd($e);
        }

        return $result;
    }

    private function createBatch(CreateTaskDiverseBatchCommand $command): Batch
    {
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
                // 'task_group_id' => $command->taskGroupId,
                'author_id' => $command->context->authorId,
                'created_at' => $command->context->handledAt,
                'updated_at' => $command->context->handledAt,
            ]);

            $taskBatchConfig = [];
            foreach ($command->configs as $config) {
                foreach ($config->taskItemGroups as $tig) {
                    $taskBatchConfig[] = [
                        'task_batch_id' => $taskBatch->id,
                        'subject_id' => $config->subjectId,
                        'subject_type' => $config->subjectType,
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

        return $batch;
    }
}
