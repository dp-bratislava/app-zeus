<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class TaskBatchConfigMapper
{
    public function __construct(
        private UniformTaskBatchConfigMapper $uniformMapper,
        private DiverseTaskBatchConfigMapper $diverseMapper,
    ) {}

    public function map(array $configuration): Collection
    {
        return match ($configuration['mode'] ?? TaskBatchMode::Uniform->value) {
            TaskBatchMode::Diverse->value => $this->diverseMapper->map($configuration),
            TaskBatchMode::Uniform->value => $this->uniformMapper->map($configuration),
            default => throw new InvalidArgumentException(
                'Invalid task batch configuration mode: '
                    . ($configuration['mode'] ?? 'null')
            ),
        };
    }
}