<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\TaskBatches\Factories\TaskBatchContextFactory;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\Profile\TaskBatchLookupScopeResolver;
use Illuminate\Support\Collection;

class TaskBatchLookupScopeService
{
    public function __construct(
        private TaskBatchLookupScopeResolver $resolver,
        private TaskBatchContextFactory $factory,
    ) {}

    public function employeeContracts(CarbonImmutable $date): Collection
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->employeeContracts($context, $date);
    }

    public function taskSubjects(): Collection
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

    public function maintenanceGroups(): Collection
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