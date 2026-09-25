<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\Factories\TaskBatchContextFactory;
use Dpb\Modules\Tasks\Resolvers\Profile\TaskBatchLookupScopeResolver;
use Illuminate\Database\Eloquent\Collection;

class TaskBatchLookupScopeService
{
    public function __construct(
        private TaskBatchLookupScopeResolver $resolver,
        private TaskBatchContextFactory $factory,
    ) {}

    public function taskSubjects(): array
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->taskSubjects($context);
    }

    public function taskGroups(): Collection
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->taskGroups($context);
    }

    public function vehicleTypes(): Collection
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->vehicleTypes($context);
    }

    public function maintenanceGroups(): array
    {
        $context = $this->factory->make();
        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->maintenanceGroups($context);
    }

    public function taskItemGroups(): Collection
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->taskItemGroups($context);
    }    
}