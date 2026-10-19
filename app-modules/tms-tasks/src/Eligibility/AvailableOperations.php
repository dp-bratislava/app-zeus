<?php

namespace Dpb\Modules\Tasks\Eligibility;

use Dpb\Modules\Tasks\Eligibility\Models\OperationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class AvailableOperations
{
    public function __construct(
        private SubjectEvaluatorRegistry $evaluators,
    ) {}

    public function for(
        int $taskItemGroupId,
        Model $subject,
    ): Collection {
        $evaluator = $this->evaluators->resolve($subject->getMorphClass());

        $subjectType = array_search(
            $subject::class,
            \Illuminate\Database\Eloquent\Relations\Relation::morphMap(),
            true,
        );

        $subjectType = $subjectType ?: $subject->getMorphClass();

        $rules = OperationRule::query()
            ->where('task_item_group_id', $taskItemGroupId)
            ->where('subject_type', $subjectType)
            ->with('operation')
            ->get();

        return $rules
            ->filter(fn (OperationRule $rule) =>
                $evaluator->matches(
                    $subject,
                    $rule->conditions ?? [],
                )
            )
            ->map(fn (OperationRule $rule) => $rule->operation)
            ->unique('id')
            ->values();
    }
}