<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskItemGroupConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumTaskSubjectType;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class TaskBatchConfigMapper
{
    public function map(array $configuration): Collection
    {
        return collect($configuration['tasks'] ?? [])
            ->map(function (array $task): TaskBatchTaskConfig {
                $groups = collect($task['task_item_groups'] ?? [])
                    ->map(fn(array $group) => new TaskBatchTaskItemGroupConfig(
                        taskItemGroupId: (int) $group['task_item_group_id'],
                        work: collect($group['work'] ?? [])
                            ->map(fn(array $work) => [
                                'contract_id' => (int) $work['contract_id'],
                                'quantity' => (int) ($work['quantity'] ?? 1),
                            ])
                            ->all(),
                    ))
                    ->all();

                $subjectType = EnumTaskSubjectType::tryFrom(
                    $task['subject_type'] ?? ''
                );

                if ($subjectType === null) {
                    throw new InvalidArgumentException(
                        'Invalid task subject type: '
                            . ($task['subject_type'] ?? 'null')
                    );
                }

                return new TaskBatchTaskConfig(
                    taskGroupId: $task['task_group_id'],
                    subject: new TaskSubjectReference(
                        $subjectType,
                        (int) $task['subject_id'],
                    ),
                    note: $task['note'] ?? '',
                    taskItemGroups: $groups,
                );
            });
    }
}
