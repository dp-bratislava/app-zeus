<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Commands\ReconcileTaskAssignmentsCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\UpdateTaskBatchWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchConfigMapper;
use Dpb\Modules\Tasks\TaskBatches\Workflows\ReconcileTaskAssignmentsWorkflow;
use Illuminate\Support\Facades\DB;

class UpdateTaskBatchWorkflow
{
    public function __construct(
        private TaskBatchConfigMapper $configMapper,
        // private TaskBatchTaskReconciler $taskReconciler,
        private ReconcileTaskAssignmentsWorkflow $reconcileTaWorkflow,
    ) {}

    public function execute(
        UpdateTaskBatchCommand $command
    ): UpdateTaskBatchWorkflowResult {
        return DB::transaction(function () use ($command) {
            $taskBatch = TaskBatch::query()
                ->with('batch')
                ->lockForUpdate()
                ->findOrFail($command->taskBatchId);


            $taReconcileWfResult = $this->reconcileTaWorkflow
                ->execute(
                    new ReconcileTaskAssignmentsCommand(
                        batchId: $taskBatch->batch->id,
                        taskBatchId: $taskBatch->id,
                        date: $command->date,
                        taskConfigs: $this->configMapper->map($command->configuration),
                        context: $command->context
                    )
                );

            $this->updateTaskBatch($taskBatch, $command);

            // reconcile batch records
            // $batch = $taskBatch->batch;
            // if ($batch !== null) {
            //     foreach ($taReconcileWfResult->recordsToCreate as $morphClass => $ids) {
            //         $batch->attachRecordIds(
            //             $morphClass,
            //             $ids
            //         );
            //     }

            //     foreach ($taReconcileWfResult->recordsToDelete as $morphClass => $ids) {
            //         DB::table('dpb_batchable_batch_records')
            //             ->whereLike('record_type', $morphClass)
            //             ->whereIn('record_id', $ids)
            //             ->where('batch_id', $batch->id)
            //             ->delete();
            //     }
            // }

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
}
