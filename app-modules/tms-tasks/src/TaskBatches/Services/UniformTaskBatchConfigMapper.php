<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\DTO\EntityIdMap;
use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskBatchConfigurationMapper;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskItemGroupConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\DTO\WtfTaskItemWork;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumTaskSubjectType;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class UniformTaskBatchConfigMapper implements TaskBatchConfigurationMapper
{
    /**
     * @TODO
     * @param array $configuration
     * @return Collection<mixed, TaskBatchTaskConfig>
     */
    public function map(array $configuration): Collection
    {
        return collect();
    }
}
