<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\Factories\TaskBatchContextFactory;
use Dpb\Modules\Tasks\Resolvers\Profile\TaskBatchRecordScopeResolver;
use Illuminate\Database\Eloquent\Builder;

class TaskBatchRecordScopeService
{
    public function __construct(
        private TaskBatchRecordScopeResolver $resolver,
        private TaskBatchContextFactory $factory,
    ) {}

    public function taskBatchScope(Builder $qeury): Builder
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->taskBatchScope($qeury, $context);
    }   
}