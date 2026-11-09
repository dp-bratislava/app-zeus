<?php

namespace Dpb\Modules\Tasks\Eligibility;

use Dpb\Modules\Tasks\Eligibility\Models\DepartmentOperationRuleView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class AvailableOperations
{
    public function __construct(
        private SubjectEvaluatorRegistry $evaluators,
    ) {}

    public function for(
        int $departmentId,
        int $taskItemGroupId,
        Model $subject,
    ): Collection {
        $subjectType = $subject->getMorphClass();

        $evaluator = $this->evaluators->resolve($subjectType);

        $rules = DepartmentOperationRuleView::query()
            ->where('department_id', $departmentId)
            ->where('task_item_group_id', $taskItemGroupId)
            ->where('subject_type', $subjectType)
            ->with('operation')
            ->get();

        return $rules
            ->filter(
                fn(DepartmentOperationRuleView $rule) =>
                $evaluator->matches(
                    $subject,
                    $rule->conditions ?? [],
                )
            )
            ->map(
                fn(DepartmentOperationRuleView $rule) =>
                $rule->operation
            )
            ->filter()
            ->unique('id')
            ->values();
    }
}
