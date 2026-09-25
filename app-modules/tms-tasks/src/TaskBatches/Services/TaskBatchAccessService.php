<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Modules\Tasks\Factories\TaskAssignmentContextFactory;
use Dpb\Modules\Tasks\Factories\TaskBatchContextFactory;
use Dpb\Modules\Tasks\Resolvers\Profile\TaskAssignmentAccessResolver;
use Dpb\Modules\Tasks\Resolvers\Profile\TaskBatchAccessResolver;

class TaskBatchAccessService
{
    public function __construct(
        private TaskBatchAccessResolver $resolver,
        private TaskBatchContextFactory $factory,
    ) {}

    public function canViewAny(): bool
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->canViewAny($context);
    }

    public function canView(): bool
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->canView($context);
    }

    public function canCreate(): bool
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->canCreate($context);
    }

    public function canUpdate(): bool
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->canUpdate($context);
    }

    public function canDelete(): bool
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->canDelete($context);
    }

    // public function canCreateTaskItem(): bool
    // {
    //     $context = $this->factory->make();

    //     return $this->resolver
    //         ->resolve($context->departmentContext->departmentGroup)
    //         ->canCreateTaskItem($context);
    // }

    // public function canEditTaskItem(): bool
    // {
    //     $context = $this->factory->make();

    //     return $this->resolver
    //         ->resolve($context->departmentContext->departmentGroup)
    //         ->canEditTaskItem($context);
    // }

    // public function canViewTaskItem(): bool
    // {
    //     $context = $this->factory->make();

    //     return $this->resolver
    //         ->resolve($context->departmentContext->departmentGroup)
    //         ->canViewTaskItem($context);
    // }

    // public function canDeleteTaskItem(): bool
    // {
    //     $context = $this->factory->make();

    //     return $this->resolver
    //         ->resolve($context->departmentContext->departmentGroup)
    //         ->canDeleteTaskItem($context);
    // }

    // public function canAssignWorkToTaskItem(TaskItem $taskItem): bool
    // {
    //     $context = $this->factory->make();

    //     return $this->resolver
    //         ->resolve($context->departmentContext->departmentGroup)
    //         ->canAssignWorkToTaskItem($taskItem, $context);
    // }
}
