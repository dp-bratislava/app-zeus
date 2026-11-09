<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchInfolistModeMapper;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchInfolistRow;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskBatchInfolistLabelResolver;

final class UniformTaskBatchInfolistMapper implements TaskBatchInfolistModeMapper
{
    public function __construct(
        private TaskBatchInfolistLabelResolver $labels,
    ) {}

    /**
     * @return list<TaskBatchInfolistRow>
     */
    public function map(array $configuration): array
    {
        $subjects = collect($configuration['subjects'] ?? [])
            ->map(
                fn ($ids) => collect((array) $ids)
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all()
            )
            ->all();

        $taskItemGroups = collect(
            $configuration['task_item_groups'] ?? []
        )
            ->mapWithKeys(
                fn ($operationIds, $groupId) => [
                    (int) $groupId => collect((array) $operationIds)
                        ->map(fn ($id) => (int) $id)
                        ->unique()
                        ->values()
                        ->all(),
                ]
            )
            ->all();

        $contractIds = collect($configuration['contract_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Resolve all display labels in batches.
        $subjectLabels = $this->labels->resolveSubjectLabels($subjects);

        $groupLabels = $this->labels->resolveTaskItemGroupLabels(
            array_keys($taskItemGroups)
        );

        $contractLabels = $this->labels->resolveContractLabels($contractIds);

        $personnel = collect($contractIds)
            ->map(
                fn (int $id) => $contractLabels[$id] ?? (string) $id
            )
            ->values()
            ->all();

        $rows = [];

        foreach ($subjects as $type => $subjectIds) {
            foreach ($subjectIds as $subjectId) {
                $subjectKey = "{$type}:{$subjectId}";
                $subject = $subjectLabels[$subjectKey] ?? [
                    'label' => (string) $subjectId,
                    'description' => '',
                ];

                foreach ($taskItemGroups as $groupId => $operationIds) {
                    $rows[] = new TaskBatchInfolistRow(
                        subjectLabel: $subject['label'],
                        subjectDescription: $subject['description'],
                        taskItemGroup: $groupLabels[$groupId]
                            ?? (string) $groupId,
                        personnel: $personnel,
                        note: '',
                    );
                }
            }
        }

        return $rows;
    }
}
