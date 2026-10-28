<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType;

final class UniformConfigurationMapper
{
    private const VERSION = '1';

    public function getConfiguration(
        array $subjects,
        int $taskGroupId,
        array $selectedTaskItemGroupIds,
        array $operationsByGroup,
        array $contractIds,
    ): array {
        $taskItemGroups = collect($selectedTaskItemGroupIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->mapWithKeys(fn (int $groupId) => [
                $groupId => collect($operationsByGroup[$groupId] ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all(),
            ])
            ->all();

        return [
            'version' => self::VERSION,
            'mode' => TaskBatchMode::Uniform->value,
            'task_group_id' => $taskGroupId,
            'subjects' => [
                TaskSubjectType::Vehicle->value => $subjects
            ],
            'task_item_groups' => $taskItemGroups,
            'contract_ids' => collect($contractIds)
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all(),
        ];
    }
}