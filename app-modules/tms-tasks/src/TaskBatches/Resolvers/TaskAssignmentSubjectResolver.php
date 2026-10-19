<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers;

use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;

class TaskAssignmentSubjectResolver
{
    public static function resolve(TaskSubjectReference $subjectReference): Model
    {
        return match ($subjectReference->type) {
            EnumsTaskSubjectType::Vehicle => Vehicle::findOrFail($subjectReference->id),
            default => throw new InvalidArgumentException(
                "Unknown subject type: {$subjectReference->type->value}"
            ),
        };
    }
}
