<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\Batchable\Models\Batch;
use Dpb\Package\Batchable\Resolvers\BatchContextResolver;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Modules\Tasks\Commands\CreateTaskAssignmentsCommand;
use Dpb\Modules\Tasks\Commands\CreateTaskBatchCommand;
use Dpb\\TaskBatch;
use Dpb\\TaskBatchTaskItemGroup;
use Dpb\\TaskBatchTaskSubject;
use Exception;
use Illuminate\Support\Facades\DB;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Modules\Tasks\Enums\TaskBatchContext;


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
                    ->execute(new CreateTaskAssignmentsCommand(
                        date: $command->date,
                        taskGroupId: $command->taskGroupId,
                        subjects: $command->subjects,
                        taskItemGroupIds: $command->taskItemGroupIds,
                        context: $command->context,
                    ));

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
        $batchContextId = BatchContextResolver::get(TaskBatchContext::VehicleCleaningB->value)?->id;

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

            $taskBatchSubjects = [];
            foreach ($command->subjects as $subject) {
                $taskBatchSubjects[] = [
                    'task_batch_id' => $taskBatch->id,
                    'subject_id' => $subject->id,
                    'subject_type' => $subject->type,
                    'created_at' => $command->context->handledAt,
                    'updated_at' => $command->context->handledAt,
                ];
            }

            TaskBatchTaskSubject::insert($taskBatchSubjects);

            // 
            $taskBatchTigs = [];
            foreach ($command->taskItemGroupIds as $taskItemGroupId) {
                $taskBatchTigs[] = [
                    'task_batch_id' => $taskBatch->id,
                    'task_item_group_id' => $taskItemGroupId,
                    'created_at' => $command->context->handledAt,
                    'updated_at' => $command->context->handledAt,
                ];
            }

            TaskBatchTaskItemGroup::insert($taskBatchTigs);
        }

        return $batch;
    }
}
