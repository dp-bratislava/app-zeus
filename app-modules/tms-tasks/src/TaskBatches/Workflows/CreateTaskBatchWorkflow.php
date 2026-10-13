<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use App\Enums\BatchContext;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Package\Batchable\Models\Batch;
use Dpb\Package\Batchable\Resolvers\BatchContextResolver;
use Exception;
use Illuminate\Support\Facades\DB;


class CreateTaskBatchWorkflow
{
    public function __construct(
        private CreateTaskAssignmentsWorkflow $taCWorkflow
    ) {}

    public function handle(
        CreateTaskBatchCommand $command,
    ) {
        $result = null;

        try {
            $result = DB::transaction(function () use ($command) {
                // handle batch
                $batch = $this->createBatch($command);

                $wfResult = $this->taCWorkflow
                    ->execute($command);

                // attach batch records
                if ($batch !== null) {
                    foreach ($wfResult->batchRecords as $morphClass => $ids) {
                        $batch->attachRecordIds(
                            $morphClass,
                            $ids
                        );
                    }
                }
            });
        } catch (Exception $e) {
            dd($e);
        }

        return $result;
    }

    private function createBatch(CreateTaskBatchCommand $command): Batch
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
                'task_group_id' => $command->taskGroupId,
                'author_id' => $command->context->authorId,
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

        return $batch;
    }
}
