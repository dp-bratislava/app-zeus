<?php

namespace App\Resolvers\Reports;

use Dpb\WorkTimeFund\Models\Maintainables\Table;
use Dpb\WorkTimeFund\Models\Maintainables\Vehicle;

class WorkActivityReportResolver
{
    public function __construct(
        protected WorkTaskSubjectResolver $wtsResolver,
        protected WorkActivityTypeResolver $watResolver,
        protected WorkActivityIsToleratedResolver $isToleratedResolver
    ) {}

    public function resolve($ctx, $maps): array
    {
        // dd($maps['workTaskSubjects']);
        $workTaskSubject = $maps['workTaskSubjects'][$ctx->task_id] ?? null;
        $activityType = $maps['activityTypes'][$ctx->activity_type][$ctx->activity_id] ?? null;
        // $activityIsTolerated = $maps['activityIsTolerated'][$ctx->activity_type][$ctx->activity_type_id] ?? null;

        return [
            'activity_id' => $ctx->activity_id,
            'activity_subject_type' => $workTaskSubject['type'] ?? null,
            'activity_subject_label' => $workTaskSubject['label'] ?? null,
            'activity_type' => $activityType['type'] ?? null,
            // 'activity_is_tolerated' => $activityIsTolerated['is_tolerated'] ?? null,
        ];
    }

    public function batchResolve($context)
    {
        $result = [];

        if (empty($context)) {
            return $result;
        }

        $taskIds = [];
        $workTaskSubjects = [];
        $activityTypes = [];
        $activityIsTolerated = [];

        foreach ($context as $ctx) {

            // work task subject
            if ($ctx->task_id) {
                $taskIds[] = $ctx->task_id;
            }

            // activity type
            if ($ctx->activity_type) {
                $activityTypes[$ctx->activity_type][] = $ctx->activity_id;
            }    
            
            // activity is tolerated
            // if ($ctx->activity_type && $ctx->task_assigned_to_id) {
            //     $activityTypes[$ctx->task_assigned_to_type][] = $ctx->task_assigned_to_id;
            // }              
        }

        $workTaskSubjectMap = $this->wtsResolver->batchResolve($taskIds);
        $activityTypeMap = $this->watResolver->batchResolve($activityTypes);
        // $activityIsToleratedMap = $this->isToleratedResolver->batchResolve($activityIsTolerated);

        $maps = [
            'workTaskSubjects' => $workTaskSubjectMap,
            'activityTypes' => $activityTypeMap,
            // 'activityIsTolerated' => $activityIsToleratedMap
        ];

        foreach ($context as $ctx) {
            $result[] = $this->resolve($ctx, $maps);
        }

        return $result;
    }

}
