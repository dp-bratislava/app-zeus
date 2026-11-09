<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchInfolistModeMapper;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchInfolistRow;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskBatchInfolistLabelResolver;

final class DiverseTaskBatchInfolistMapper implements TaskBatchInfolistModeMapper
{
    public function __construct(
        private TaskBatchInfolistLabelResolver $labels,
    ) {}

    /**
     * @return list<TaskBatchInfolistRow>
     */
    public function map(array $configuration): array
    {
        $tasks = collect($configuration['tasks'] ?? []);

        // Collect IDs first so label lookups are batched.
        $subjects = $tasks
            ->groupBy('subject_type')
            ->map(
                fn ($group) => $group
                    ->pluck('subject_id')
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all()
            )
            ->all();

        $taskItems = $tasks->flatMap(
            fn (array $task) => $task['task_items'] ?? []
        );

        $groupIds = $taskItems
            ->pluck('task_item_group_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $contractIds = $taskItems
            ->flatMap(fn (array $item) => $item['work'] ?? [])
            ->pluck('contract_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $subjectLabels = $this->labels->resolveSubjectLabels($subjects);
        $groupLabels = $this->labels->resolveTaskItemGroupLabels($groupIds);
        $contractLabels = $this->labels->resolveContractLabels($contractIds);

        $rows = [];

        foreach ($tasks as $task) {
            $subjectType = $task['subject_type'] ?? '';
            $subjectId = (int) ($task['subject_id'] ?? 0);

            $subject = $subjectLabels["{$subjectType}:{$subjectId}"] ?? [
                'label' => (string) $subjectId,
                'description' => '',
            ];

            foreach ($task['task_items'] ?? [] as $item) {
                $groupId = (int) $item['task_item_group_id'];

                $personnel = collect($item['work'] ?? [])
                    ->map(function (array $work) use ($contractLabels): string {
                        $contractId = (int) $work['contract_id'];
                        $quantity = (int) ($work['quantity'] ?? 1);

                        $label = $contractLabels[$contractId]
                            ?? (string) $contractId;

                        return $label . ($quantity > 1 ? " × {$quantity}" : '');
                    })
                    ->values()
                    ->all();

                $rows[] = new TaskBatchInfolistRow(
                    subjectLabel: $subject['label'],
                    subjectDescription: $subject['description'],
                    taskItemGroup: $groupLabels[$groupId]
                        ?? (string) $groupId,
                    personnel: $personnel,
                    note: $task['note'] ?? '',
                );
            }
        }

        return $rows;
    }
}
