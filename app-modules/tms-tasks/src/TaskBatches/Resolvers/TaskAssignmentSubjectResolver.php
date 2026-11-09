<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers;

use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;
use Illuminate\Support\Collection;

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

    /**
     * @param Collection<TaskSubjectReference> $subjectReferences
     * @return Collection<string, Model>
     */
    public static function resolveMany(Collection $subjectReferences): Collection
    {
        $resolved = collect();

        foreach ($subjectReferences->groupBy(
            fn (TaskSubjectReference $subject) => $subject->type->value
        ) as $type => $references) {
            $ids = $references
                ->pluck('id')
                ->unique()
                ->values();

            $models = match ($type) {
                EnumsTaskSubjectType::Vehicle->value =>
                    Vehicle::query()->whereKey($ids)->get(),

                default => throw new InvalidArgumentException(
                    "Unknown subject type: {$type}"
                ),
            };

            foreach ($models as $model) {
                $key = "{$type}:{$model->getKey()}";
                $resolved->put($key, $model);
            }
        }

        return $resolved;
    }    
}
