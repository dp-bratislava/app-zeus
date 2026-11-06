<?php

namespace Dpb\Modules\Tasks\TaskBatches\Exceptions;

use RuntimeException;

final class TaskGroupModeNotFoundException extends RuntimeException
{
    public static function for(
        int $taskGroupId,
        int $departmentId,
    ): self {
        return new self(
            sprintf(
                'No task batch mode configured for task group [%d] and department [%d].',
                $taskGroupId,
                $departmentId,
            ),
        );
    }
}