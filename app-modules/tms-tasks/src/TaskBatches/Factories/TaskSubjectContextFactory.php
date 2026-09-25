<?php

namespace Dpb\Modules\Tasks\Factories;

use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Modules\Tasks\Context\TaskSubjectContext;
use Dpb\Modules\Tasks\Resolvers\TaskSubjectAdapterResolver;
use RuntimeException;

class TaskSubjectContextFactory
{
    public function __construct(
        private TaskSubjectAdapterResolver $resolver
    ) {}

    public function make(object $subject): TaskSubjectContext
    {
        $type = match (true) {
            $subject instanceof Vehicle => 'vehicle',
            default => throw new RuntimeException('Unknown subject'),
        };

        $adapter = $this->resolver->resolve($type);

        return $adapter->toTaskContext($subject);
    }
}