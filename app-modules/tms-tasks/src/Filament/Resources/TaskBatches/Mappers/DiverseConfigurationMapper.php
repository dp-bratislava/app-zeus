<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Dpb\Modules\Tasks\Eligibility\AvailableOperations;
use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskAssignmentSubjectResolver;
use Dpb\WorkTimeFund\Models\Operation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class DiverseConfigurationMapper
{
    private const VERSION = '1';
    private const MODE = TaskBatchMode::Diverse->value;

    public function __construct(
        private readonly AvailableOperations $availableOperations,
        private readonly TaskAssignmentSubjectResolver $subjectResolver,
    ) {}

    public function getConfiguration(
        array $matrix,
        array $notes = [],
    ): array {
        $subjectType = EnumsTaskSubjectType::Vehicle;

        $subjectReferences = collect(array_keys($matrix))
            ->map(fn($id) => new TaskSubjectReference(
                type: $subjectType,
                id: (int) $id,
            ));

        $subjects = $this->subjectResolver->resolveMany($subjectReferences);

        return [
            'version' => self::VERSION,
            'mode' => self::MODE,
            'tasks' => collect($matrix)
                ->map(function (array $groups, int|string $subjectId) use (
                    $notes,
                    $subjects,
                    $subjectType,
                ): array {
                    $subjectId = (int) $subjectId;

                    $subject = $subjects->get(
                        "{$subjectType->value}:{$subjectId}"
                    );

                    if ($subject === null) {
                        throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                            "Subject not found: {$subjectType->value}:{$subjectId}"
                        );
                    }

                    return [
                        'subject_type' => $subjectType->value,
                        'subject_id' => $subjectId,
                        'note' => $notes[$subjectId] ?? '',
                        'task_items' => collect($groups)
                            ->filter(fn(array $cell) => ! empty($cell['work']))
                            ->map(fn(array $cell, int|string $groupId): array => [
                                'task_item_group_id' => (int) $groupId,
                                'operations' => $this->mapOperations(
                                    (int) $groupId,
                                    $subject,
                                ),
                                'work' => $this->mapWork(
                                    collect($cell['work']),
                                ),
                            ])
                            ->values()
                            ->all(),
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    private function mapOperations(
        int $tigId,
        Model $subject,
    ): array {
        return $this->availableOperations
            ->for($tigId, $subject)
            ->map(fn(Operation $operation) => (int) $operation->id)
            ->values()
            ->all();
    }

    private function mapWork(Collection $workData): array
    {
        return $workData
            ->filter(
                fn($item) => is_array($item)
                    && isset($item['contract_id']),
            )
            ->map(fn(array $item): array => [
                'contract_id' => (int) $item['contract_id'],
                'quantity' => (int) ($item['quantity'] ?? 1),
            ])
            ->values()
            ->all();
    }
}
