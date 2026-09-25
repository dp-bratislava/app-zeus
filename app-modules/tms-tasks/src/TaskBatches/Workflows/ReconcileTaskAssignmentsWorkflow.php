<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Modules\Tasks\Commands\CreateTaskAssignmentsCommand;
use Dpb\Modules\Tasks\Commands\ReconcileTaskItemsCommand;
use Dpb\Modules\Tasks\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\DTO\CreateTaskAssignmentsWorkflowResult;
use Dpb\Modules\Tasks\DTO\DeleteTaskAssignmentsWorkflowResult;
use Dpb\Modules\Tasks\DTO\ReconcileTaskAssignmentsWorkflowResult;
use Dpb\Modules\Tasks\DTO\ReconcileTaskItemsWorkflowResult;
use Dpb\Modules\Tasks\DTO\TaskSubjectReference;
use Dpb\\TaskBatch;
use Dpb\Modules\Tasks\Workflows\CreateTaskAssignmentsWorkflow;
use Dpb\Modules\Tasks\Workflows\DeleteTaskAssignmentsWorkflow;
use Dpb\Modules\Tasks\Workflows\ReconcileTaskItemsWorkflow;
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
        UpdateTaskBatchCommand $command,
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
        UpdateTaskBatchCommand $command
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
        UpdateTaskBatchCommand $command,
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
        UpdateTaskBatchCommand $command,
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
