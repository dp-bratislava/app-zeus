<?php

namespace Dpb\Modules\Tasks\TaskBatches\Contracts;

use Dpb\Modules\Tasks\TaskBatches\Context\TaskSubjectContext;

interface TaskSubjectAdapter
{
    public function toTaskContext(object $subject): TaskSubjectContext;
}