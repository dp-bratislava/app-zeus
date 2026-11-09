<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers;

use Dpb\Modules\Tasks\TaskBatches\Contracts\TaskSubjectAdapter;
use RuntimeException;

class TaskSubjectAdapterResolver
{
    public function resolve(string $type): TaskSubjectAdapter
    {
        $map = config('dpb-wtf-tms-bridge.task_subject_adapters');

        if (! isset($map[$type])) {
            throw new RuntimeException("No adapter for type: {$type}");
        }

        return app($map[$type]);
    }
}
