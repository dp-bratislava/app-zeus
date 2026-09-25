<?php

namespace Dpb\Modules\Tasks\Workflows;

use Carbon\CarbonImmutable;
use Dpb\\TaskBatch;
use Illuminate\Support\Facades\DB;
use Dpb\Modules\Tasks\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\DTO\TaskSubjectReference;
use Dpb\\TaskBatchTaskSubject;
use Dpb\Modules\Tasks\Workflows\ReconcileTaskAssignmentsWorkflow;

class UpdateTaskBatchWorkflow
{
    public function __construct(
        // private TaskBatchTaskReconciler $taskReconciler,
        private ReconcileTaskAssignmentsWorkflow $reconcileTaWorkflow,
    ) {}

    public function execute(UpdateTaskBatchCommand $command): void
    {
        DB::transaction(function () use ($command) {
            $taskBatch = TaskBatch::query()
                ->lockForUpdate()
                ->findOrFail($command->taskBatchId);


            $reconcileResult = $this->reconcileTaWorkflow
                ->execute($taskBatch, $command);

            $this->updateTaskBatch($taskBatch, $command);

            // reconcile batch records
            $batch = $taskBatch->batch;
            if ($batch !== null) {
                foreach ($reconcileResult->recordsToCreate as $morphClass => $ids) {
                    $batch->attachRecordIds(
                        $morphClass,
                        $ids
                    );
                }

                foreach ($reconcileResult->recordsToDelete as $morphClass => $ids) {
                    DB::table('dpb_batchable_batch_records')
                        ->whereLike('record_type', $morphClass)
                        ->whereIn('record_id', $ids)
                        ->where('batch_id', $batch->id)
                        ->delete();
                }
            }
        });
    }

    private function updateTaskBatch(
        TaskBatch $taskBatch,
        UpdateTaskBatchCommand $command,
    ): void {
        $taskBatch->update([
            'date' => $command->date,
            'task_group_id' => $command->taskGroupId,
        ]);

        $this->syncSubjects(
            $taskBatch,
            $command->subjects,
        );

        $this->syncItemGroups(
            $taskBatch,
            $command->taskItemGroupIds,
            $command->context->handledAt
        );
    }

    private function syncSubjects(
        TaskBatch $taskBatch,
        array $subjects,
    ): void {
        $desired = collect($subjects)
            ->map(static fn(TaskSubjectReference $subject): array => [
                'subject_id' => $subject->id,
                'subject_type' => $subject->type->value,
            ])
            ->unique(
                static fn(array $subject): string =>
                "{$subject['subject_type']}:{$subject['subject_id']}"
            )
            ->values();

        $existing = $taskBatch->subjects()
            ->get()
            ->keyBy(
                static fn(TaskBatchTaskSubject $subject): string =>
                "{$subject->subject_type}:{$subject->subject_id}"
            );

        // Create newly added subjects.
        foreach ($desired as $subject) {
            $key = "{$subject['subject_type']}:{$subject['subject_id']}";

            if (! $existing->has($key)) {
                $taskBatch->subjects()->create($subject);
            }
        }

        // Remove subjects no longer selected.
        $desiredKeys = $desired->map(
            static fn(array $subject): string =>
            "{$subject['subject_type']}:{$subject['subject_id']}"
        );

        $existing
            ->reject(
                static fn(
                    TaskBatchTaskSubject $subject,
                    string $key,
                ): bool => $desiredKeys->contains($key)
            )
            ->each
            ->delete();
    }

    private function syncItemGroups(
        TaskBatch $taskBatch,
        array $taskItemGroupIds,
        CarbonImmutable $updatedAt,
    ): void {
        $desiredIds = collect($taskItemGroupIds)
            ->map(static fn(int|string $id): int => (int) $id)
            ->unique()
            ->values();

        $existingIds = $taskBatch->itemGroups()
            ->pluck('task_item_group_id');

        $idsToDelete = $existingIds->diff($desiredIds);

        if ($idsToDelete->isNotEmpty()) {
            $taskBatch->itemGroups()
                ->whereIn('task_item_group_id', $idsToDelete)
                ->delete();
        }

        $idsToCreate = $desiredIds->diff($existingIds);

        if ($idsToCreate->isNotEmpty()) {
            $now = now();

            $taskBatch->itemGroups()->insert(
                $idsToCreate->map(static fn(int $id): array => [
                    'task_batch_id' => $taskBatch->id,
                    'task_item_group_id' => $id,
                    'created_at' => $updatedAt,
                    'updated_at' => $updatedAt,
                ])->all(),
            );
        }
    }
}
