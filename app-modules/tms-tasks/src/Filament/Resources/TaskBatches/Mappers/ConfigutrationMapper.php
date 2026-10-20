<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;

class ConfigutrationMapper
{
    private const VERSION = '1';

    public function getConfiguration(
        array $matrix,
        array $notes = []
    ): array {
        return [
            'version' => self::VERSION,
            'tasks' => collect($matrix)
                ->map(fn(array $groups, int|string $subjectId) => [
                    // TODO
                    'task_group_id' => 9,

                    'subject_type' => EnumsTaskSubjectType::Vehicle->value,
                    'subject_id' => (int) $subjectId,
                    'note' => $notes[(int) $subjectId] ?? '',
                    'task_item_groups' => collect($groups)
                        ->filter(fn(array $cell) => ! empty($cell['work']))
                        ->map(fn(array $cell, int|string $groupId) => [
                            'task_item_group_id' => (int) $groupId,
                            'work' => collect($cell['work'] ?? [])
                                ->filter(fn($work) => is_array($work)
                                    && isset($work['contract_id']))
                                ->map(fn(array $work) => [
                                    'contract_id' => (int) $work['contract_id'],
                                    'quantity' => (int) ($work['quantity'] ?? 1),
                                ])
                                ->values()
                                ->all(),
                        ])
                        ->values()
                        ->all(),
                ])
                ->values()
                ->all(),
        ];
    }
}
