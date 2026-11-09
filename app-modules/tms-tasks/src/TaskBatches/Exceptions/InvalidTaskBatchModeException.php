<?php

namespace Dpb\Modules\Tasks\TaskBatches\Exceptions;

use RuntimeException;

final class InvalidTaskBatchModeException extends RuntimeException
{
    public static function for(
        int $mode,
    ): self {
        return new self(
            sprintf(
                'No enum value defined for TaskBatchMode [%s].',
                $mode,
            ),
        );
    }
}