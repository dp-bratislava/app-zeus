<?php

namespace Dpb\Modules\Tasks\TaskBatches\Contracts;

use Illuminate\Support\Collection;

interface TaskBatchConfigurationMapper
{
    public function map(array $configuration): Collection;
}
