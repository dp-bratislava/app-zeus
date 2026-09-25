<?php

namespace Dpb\Modules\Tasks\Contracts;

use Dpb\Modules\Tasks\Context\TaskSubjectContext;

interface TaskSubjectAdapter
{
    public function toTaskContext(object $subject): TaskSubjectContext;
}