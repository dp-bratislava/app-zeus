<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskAssignmentsCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\ReconcileTaskItemsCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\UpdateTaskUniformBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\CreateTaskAssignmentsWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\DTO\DeleteTaskAssignmentsWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\DTO\ReconcileTaskAssignmentsWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\DTO\ReconcileTaskItemsWorkflowResult;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Workflows\CreateTaskAssignmentsWorkflow;
use Dpb\Modules\Tasks\TaskBatches\Workflows\DeleteTaskAssignmentsWorkflow;
use Dpb\Modules\Tasks\TaskBatches\Workflows\ReconcileTaskItemsWorkflow;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Tasks\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReconcileTaskAssignmentsWorkflow
{
    public function __construct(
        private CreateTaskAssignmentsWorkflow $taCWorkflow,
        private DeleteTaskAssignmentsWorkflow $taDWorkflow,
        private ReconcileTaskItemsWorkflow $tiReconcileWorkflow,

    ) {}

    public function execute(
        TaskBatch $taskBatch,
        UpdateTaskUniformBatchCommand $command,
    ): ReconcileTaskAssignmentsWorkflowResult {
        $batchId = $taskBatch->batch->id;

        $existingAssignments = $this->getExistingTaskAssignments(
            $batchId
        );

        $existingAssignmentsBySubject = $existingAssignments->keyBy(
            fn($assignment) =>
            "{$assignment->subject_type}:{$assignment->subject_id}"
        );

        $desiredSubjects = collect($command->subjects)->keyBy(
            fn(TaskSubjectReference $subject) =>
            "{$subject->type->value}:{$subject->id}"
        );

        $subjectsToCreateAssignmentsFor = $desiredSubjects->diffKeys($existingAssignmentsBySubject);
        $assignmentsToDelete = $existingAssignmentsBySubject->diffKeys($desiredSubjects);
        $assignmentsToKeep = $existingAssignmentsBySubject->intersectByKeys($desiredSubjects);

        // create
        $createRecords = $this->createAssignments($subjectsToCreateAssignmentsFor, $command);

        // delete
        $deleteRecords = $this->deleteAssignments($assignmentsToDelete);

        // update
        $taskIds = $assignmentsToKeep->pluck('task_id')->all();

        $this->updateAssignments(
            $taskIds,
            $command,
        );

        // reconcile task items
        $rtiwResult = $this->reconcileTaskItems(
            $batchId,
            $taskIds,
            $command
        );

        return new ReconcileTaskAssignmentsWorkflowResult(
            recordsToCreate: array_merge(
                $createRecords?->batchRecords ?? [],
                $rtiwResult->recordsToCreate
            ),
            recordsToDelete: array_merge(
                $deleteRecords?->batchRecords ?? [],
                $rtiwResult->recordsToDelete
            )
        );
    }

    private function getExistingTaskAssignments(
        int $batchId,
    ): Collection {
        $taskAssignmentMorph = app(TaskAssignment::class)->getMorphClass();

        return DB::table('dpb_batchable_batch_records as br')
            ->join(
                'tms_task_assignments as ta',
                'ta.id',
                '=',
                'br.record_id'
            )
            ->where('br.batch_id', $batchId)
            ->where('br.record_type', $taskAssignmentMorph)
            ->whereNull('ta.deleted_at')
            ->get([
                'ta.id',
                'ta.subject_id',
                'ta.subject_type',
                'ta.task_id',
            ]);
    }

    private function createAssignments(
        Collection $subjectsToCreateFor,
        UpdateTaskUniformBatchCommand $command
    ): ?CreateTaskAssignmentsWorkflowResult {
        if ($subjectsToCreateFor->isEmpty()) {
            return null;
        }

        return $this->taCWorkflow
            ->execute(new CreateTaskAssignmentsCommand(
                date: $command->date,
                taskGroupId: $command->taskGroupId,
                subjects: $subjectsToCreateFor->values()->all(),
                taskItemGroupIds: $command->taskItemGroupIds,
                context: $command->context
            ));
    }

    private function deleteAssignments(
        Collection $assignmentsToDelete
    ): ?DeleteTaskAssignmentsWorkflowResult {
        if ($assignmentsToDelete->isEmpty()) {
            return null;
        }

        return $this->taDWorkflow
            ->execute($assignmentsToDelete->pluck('id')->toArray());
    }

    private function updateAssignments(
        array $taskIds,
        UpdateTaskUniformBatchCommand $command,
    ): void {
        Task::query()
            ->whereIn('id', $taskIds)
            ->update([
                'group_id' => $command->taskGroupId,
                'date' => $command->date,
                'updated_at' => $command->context->handledAt,
            ]);
    }

    private function reconcileTaskItems(
        int $batchId,
        array $taskIds,
        UpdateTaskUniformBatchCommand $command,
    ): ReconcileTaskItemsWorkflowResult {

        return $this->tiReconcileWorkflow
            ->execute(
                new ReconcileTaskItemsCommand(
                    batchId: $batchId,
                    date: $command->date,
                    taskIds: $taskIds,
                    taskItemGroupIds: $command->taskItemGroupIds,
                    context: $command->context,
                )
            );
    }
}
