<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\Eligibility\AvailableOperations;
use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskAssignmentSubjectResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class UniformConfigurationMapper
{
    private const VERSION = '1';
    private const MODE = TaskBatchMode::Uniform;

    public function __construct(
        private readonly AvailableOperations $availableOperations,
        private readonly TaskAssignmentSubjectResolver $subjectResolver,
    ) {}

    public function getConfiguration(
            array $subjects,
            int $taskGroupId,
            array $taskItemGroups,
            array $contractIds
    ): array {
        return [
            'version' => self::VERSION,
            'mode' => TaskBatchMode::Uniform->value,
            'task_group_id' => $taskGroupId,
            'subjects' => $subjects,
            'task_item_groups' => collect($taskItemGroups)
                ->mapWithKeys(fn (array $operationIds, int|string $groupId) => [
                    (int) $groupId => collect($operationIds)
                        ->map(fn ($id) => (int) $id)
                        ->unique()
                        ->values()
                        ->all(),
                ])
                ->all(),
            'contract_ids' => collect($contractIds)
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all(),
        ];
    }
}