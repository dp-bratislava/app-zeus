<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers;

use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;
use Dpb\WorkTimeFund\Models\Maintainables\Vehicle as MaintainablesVehicle;

class WtfActivityMaintainableResolver
{
    public static function resolve(TaskSubjectReference $subjectReference): Model
    {
        return match ($subjectReference->type) {
            EnumsTaskSubjectType::Vehicle => MaintainablesVehicle::findOrFail($subjectReference->id),
            default => throw new InvalidArgumentException(
                "Unknown subject type: {$subjectReference->type->value}"
            ),
        };
    }
}
