<?php

namespace App\Resolvers\Snapshots;

class TaskItemSnapshotResolver
{
    public function __construct(
        protected TaskSubjectResolver $taSubjectResolver,
        protected TaskAssignedToResolver $taAssignedToResolver,
        protected TaskItemAssignedToResolver $tiaAssignedToResolver,
        protected TaskRequestedForResolver $trfAssignedToResolver,
    ) {}

    public function resolve($ctx, $maps): array
    {
        $taskSubject = $maps['taskSubject'][$ctx->task_subject_type][$ctx->task_subject_id] ?? null;
        $taskAssignedTo = $maps['taskAssignedTo'][$ctx->task_assigned_to_type][$ctx->task_assigned_to_id] ?? null;
        $taskRequestedFor = $maps['taskRequestedFor'][$ctx->task_requested_for_type][$ctx->task_requested_for_id] ?? null;
        $taskItemAssignedTo = $maps['taskItemAssignedTo'][$ctx->task_item_assigned_to_type][$ctx->task_item_assigned_to_id] ?? null;

        // dd($taskSubject->label);
        return [
            'task_item_id' => $ctx->task_item_id,
            'task_assigned_to_type' => $ctx->task_assigned_to_type,
            'task_assigned_to_label' => $taskAssignedTo['label'] ?? null,
            'task_requested_for_type' => $ctx->task_requested_for_type,
            'task_requested_for_label' => $taskRequestedFor['label'] ?? null,
            'task_subject_type' => $ctx->task_subject_type,
            'task_subject_label' => $taskSubject['label'] ?? null,
            'task_item_assigned_to_type' => $ctx->task_item_assigned_to_type,
            'task_item_assigned_to_label' => $taskItemAssignedTo['label'] ?? null,
        ];
    }

    public function batchResolve($context)
    {
        $result = [];

        if (empty($context)) {
            return $result;
        }

        // 
        $taskSubjects = [];
        $taskAssignedTo = [];
        $taskRequestedFor = [];
        $taskItemAssignedTo = [];

        foreach ($context as $ctx) {

            // task subject
            if ($ctx->task_subject_type && $ctx->task_subject_id) {
                $taskSubjects[$ctx->task_subject_type][] = $ctx->task_subject_id;
            }
            // task assigned to
            if ($ctx->task_assigned_to_type && $ctx->task_assigned_to_id) {
                $taskAssignedTo[$ctx->task_assigned_to_type][] = $ctx->task_assigned_to_id;
            }

            // task requested for
            if ($ctx->task_requested_for_type && $ctx->task_requested_for_id) {
                $taskRequestedFor[$ctx->task_requested_for_type][] = $ctx->task_requested_for_id;
            }

            // task item assigned to
            if ($ctx->task_item_assigned_to_type && $ctx->task_item_assigned_to_id) {
                $taskItemAssignedTo[$ctx->task_item_assigned_to_type][] = $ctx->task_item_assigned_to_id;
            }
        }

        $taskSubjectsMap = $this->taSubjectResolver->preload($taskSubjects);
        $taskAssignedToMap = $this->taAssignedToResolver->preload($taskAssignedTo);
        $taskRequestedForMap = $this->trfAssignedToResolver->preload($taskRequestedFor);
        $taskItemAssignedToMap = $this->tiaAssignedToResolver->preload($taskItemAssignedTo);

        $maps = [
            'taskSubject' => $taskSubjectsMap,
            'taskAssignedTo' => $taskAssignedToMap,
            'taskRequestedFor' => $taskRequestedForMap,
            'taskItemAssignedTo' => $taskItemAssignedToMap,
        ];

        foreach ($context as $ctx) {
            $result[] = $this->resolve($ctx, $maps);
        }

        return $result;
    }
}
