<?php

namespace App\Resolvers\Reports;

use Dpb\WorkTimeFund\Models\Maintainables\Table;
use Dpb\WorkTimeFund\Models\Maintainables\Vehicle;

class WorkActivityReportResolver
{
    public function __construct(
        protected WorkTaskSubjectResolver $wtsResolver
    ) {}

    public function resolve($ctx, $maps): array
    {
        $activitySubject = $maps['taskAssignedTo'][$ctx->activity_subject_type][$ctx->task_assigned_to_id] ?? null;

        return [
            'activity_id' => $ctx->activity_id,
            'activity_subject_type' => $ctx->activity_subject_type,
            'activity_subject_label' => $activitySubject?->label ?? null,
        ];
    }

    public function batchResolve($context)
    {
        $result = [];

        if (empty($context)) {
            return $result;
        }

        $workTaskSubjects = [];

        foreach ($context as $ctx) {

            // work task subject
            if ($ctx->task_assigned_to_type && $ctx->task_assigned_to_id) {
                $workTaskSubjects[$ctx->task_assigned_to_type][] = $ctx->task_assigned_to_id;
            }
        }

        $workTaskSubjectMap = $this->wtsResolver->preload($workTaskSubjects);

        $maps = [
            'workTaskSubjects' => $workTaskSubjectMap,
        ];

        foreach ($context as $ctx) {
            $result[] = $this->resolve($ctx, $maps);
        }

        return $result;
    }

}
