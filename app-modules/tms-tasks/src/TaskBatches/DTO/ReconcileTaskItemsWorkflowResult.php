<?php

namespace Dpb\Modules\Tasks\TaskBatches\DTO;

/**
 * List of ids of created entites
 */
final readonly class ReconcileTaskItemsWorkflowResult
{
    public function __construct(
        public array $recordsToCreate,
        public array $recordsToDelete,
    ) {}
}