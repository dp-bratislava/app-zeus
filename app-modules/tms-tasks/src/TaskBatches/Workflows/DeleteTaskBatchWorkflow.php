<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\\TaskBatch;
use Illuminate\Support\Facades\DB;

class DeleteTaskBatchWorkflow
{
    public function __construct(
        private DeleteTaskAssignmentsWorkflow $taDWorkflow
    ) {}

    public function handle(TaskBatch $taskBatch)
    {
        // delete tasks and work
        $this->deleteTaskAssignments($taskBatch->batch->id);
        // delete task batch and batch
        $this->deleteBatch($taskBatch);
    }

    private function deleteTaskAssignments(int $batchId)
    {
        $taskAssignmentMorph = app(TaskAssignment::class)->getMorphClass();

        $existingAssignmentIds = DB::table('dpb_batchable_batch_records as br')
            ->join(
                'tms_task_assignments as ta',
                'ta.id',
                '=',
                'br.record_id'
            )
            ->where('br.batch_id', $batchId)
            ->where('br.record_type', $taskAssignmentMorph)
            ->whereNull('ta.deleted_at')
            ->pluck(
                'ta.id'
            );

        $this->taDWorkflow->execute($existingAssignmentIds->toArray());
    }

    private function deleteBatch(TaskBatch $taskBatch)
    {
        // delete batch records
        DB::table('dpb_batchable_batch_records as br')
            ->where('br.batch_id', $taskBatch->batch_id)
            ->delete();

        // delete batch
        $taskBatch->batch->delete();

        // delete task batch
        $taskBatch->delete();
    }
}
