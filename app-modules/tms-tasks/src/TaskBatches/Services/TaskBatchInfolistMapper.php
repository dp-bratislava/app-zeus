<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchInfolistRow;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class TaskBatchInfolistMapper
{
    public function __construct(
        private UniformTaskBatchInfolistMapper $uniformMapper,
        private DiverseTaskBatchInfolistMapper $diverseMapper,
    ) {}

    public function map(Model $record): array
    {
        $configuration = $record->configuration ?? [];

        if (is_string($configuration)) {
            $configuration = json_decode($configuration, true) ?? [];
        }

        if (! is_array($configuration)) {
            $configuration = [];
        }

        $rows = match ($configuration['mode'] ?? null) {
            'uniform' => $this->uniformMapper->map($configuration),
            'diverse' => $this->diverseMapper->map($configuration),
            default => throw new InvalidArgumentException(
                'Unsupported task batch configuration mode.'
            ),
        };

        return array_map(
            fn(TaskBatchInfolistRow $row): array => $row->toArray(),
            $rows,
        );
    }
}
