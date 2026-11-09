<?php

namespace Dpb\Modules\Tasks\TaskBatches\Contracts;

use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchInfolistRow;

interface TaskBatchInfolistModeMapper
{
    /**
     * @return list<TaskBatchInfolistRow>
     */    
    public function map(array $configuration): array;
}
