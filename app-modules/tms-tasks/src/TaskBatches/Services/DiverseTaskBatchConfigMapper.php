<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchConfigurationMapper;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskItemConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\DTO\WtfTaskItemWork;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumTaskSubjectType;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class DiverseTaskBatchConfigMapper implements TaskBatchConfigurationMapper
{
    public function map(array $configuration): Collection
    {
        return collect($configuration['tasks'] ?? [])
            ->map(fn(array $task) => $this->mapTask($task));
    }

    private function mapTask(array $task): TaskBatchTaskConfig
    {
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
            taskGroupId: (int) $task['task_group_id'],
            subject: new TaskSubjectReference(
                $subjectType,
                (int) $task['subject_id'],
            ),
            note: $task['note'] ?? '',
            taskItemConfigs: collect($task['task_items'] ?? [])
                ->map(fn(array $item) => $this->mapTaskItem($item))
                ->all(),
        );
    }


    private function mapTaskItem(array $item): TaskBatchTaskItemConfig
    {
        return new TaskBatchTaskItemConfig(
            taskItemGroupId: (int) $item['task_item_group_id'],
            operationIds: collect($item['operations'] ?? [])
                ->map(fn ($operationId) => (int) $operationId)
                ->all(),
            work: collect($item['work'] ?? [])
                ->map(fn (array $work) => $this->mapWork($work)),
        );
    }

    private function mapWork(array $work): WtfTaskItemWork
    {
        return new WtfTaskItemWork(
            contractId: (int) $work['contract_id'],
            quantity: (int) ($work['quantity'] ?? 1),
        );
    }
}
