<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

final readonly class WtfTaskItemWork
{
    /**
     * Summary of __construct
     * @param int $contractId
     * @param int $quantity
     */
    public function __construct(
        public int $contractId,
        public int $quantity,
    ) {}
}