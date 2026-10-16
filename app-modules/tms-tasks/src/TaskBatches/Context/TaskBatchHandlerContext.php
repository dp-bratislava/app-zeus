<?php

namespace Dpb\Modules\Tasks\TaskBatches\Context;

use Carbon\CarbonImmutable;

final readonly class TaskBatchHandlerContext
{
    /**     
     * @param int $placeOfOriginId
     * @param int $authorId
     * @param CarbonImmutable $handledAt
     * @param mixed $departmentId
     */
    public function __construct(
        public int $placeOfOriginId,
        public int $authorId,
        public CarbonImmutable $handledAt,
        public ?int $departmentId = null,
    ) {}
}
