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

final class UniformTaskBatchConfigMapper implements TaskBatchConfigurationMapper
{
    public function map(array $configuration): Collection
    {
        // return collect();
        $taskGroupId = (int) $configuration['task_group_id'];

        $taskItemConfigs = collect(
            $configuration['task_item_groups'] ?? []
        )
            ->map(
                fn(array $operationIds, int|string $taskItemGroupId) =>
                new TaskBatchTaskItemConfig(
                    taskItemGroupId: (int) $taskItemGroupId,
                    operationIds: collect($operationIds)
                        ->map(fn($id) => (int) $id)
                        ->values()
                        ->all(),
                    work: collect($configuration['contract_ids'] ?? [])
                        ->map(
                            fn($contractId) => new WtfTaskItemWork(
                                contractId: (int) $contractId,
                                quantity: 1,
                            )
                        ),
                )
            )
            ->values()
            ->all();

        $tasks = collect();

        foreach ($configuration['subjects'] ?? [] as $subjectType => $subjectIds) {
            $enumSubjectType = EnumTaskSubjectType::tryFrom($subjectType);

            if ($enumSubjectType === null) {
                throw new InvalidArgumentException(
                    "Invalid task subject type: {$subjectType}"
                );
            }


            foreach ((array) $subjectIds as $subjectId) {
                $tasks->push(
                    new TaskBatchTaskConfig(
                        taskGroupId: $taskGroupId,
                        subject: new TaskSubjectReference(
                            $enumSubjectType,
                            (int) $subjectId,
                        ),
                        note: '',
                        taskItemConfigs: $taskItemConfigs,
                    )
                );
            }
        }

        return $tasks;
    }
}
