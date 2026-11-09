<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Modules\Tasks\TaskBatches\Factories\TaskAssignmentContextFactory;
use Dpb\Modules\Tasks\TaskBatches\Factories\TaskBatchContextFactory;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\Profile\TaskAssignmentAccessResolver;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\Profile\TaskBatchAccessResolver;
use Dpb\Package\Tasks\Models\TaskItem;

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

    public function canView(TaskBatch $record): bool
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->canView($record, $context);
    }

    public function canCreate(): bool
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->canCreate($context);
    }

    public function canUpdate(TaskBatch $record): bool
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->canUpdate($record, $context);
    }

    public function canDelete(TaskBatch $record): bool
    {
        $context = $this->factory->make();

        return $this->resolver
            ->resolve($context->departmentContext->departmentGroup)
            ->canDelete($record, $context);
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
