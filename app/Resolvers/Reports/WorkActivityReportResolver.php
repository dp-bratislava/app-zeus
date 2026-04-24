<?php

namespace App\Resolvers\Reports;

class WorkActivityReportResolver
{
    public function __construct(
        protected WorkActivityTypeResolver $watResolver,
        protected WorkActivityIsToleratedResolver $isToleratedResolver
    ) {}

    public function resolve($ctx, $maps): array
    {
        $activityType = $maps['activityTypes'][$ctx->activity_type][$ctx->activity_id] ?? null;
        // $activityIsTolerated = $maps['activityIsTolerated'][$ctx->activity_type][$ctx->activity_type_id] ?? null;

        return [
            'activity_id' => $ctx->activity_id,
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

        $activityTypes = [];
        $activityIsTolerated = [];

        foreach ($context as $ctx) {

            // activity type
            if ($ctx->activity_type) {
                $activityTypes[$ctx->activity_type][] = $ctx->activity_id;
            }    
            
            // activity is tolerated
            // if ($ctx->activity_type && $ctx->task_assigned_to_id) {
            //     $activityTypes[$ctx->task_assigned_to_type][] = $ctx->task_assigned_to_id;
            // }              
        }

        $activityTypeMap = $this->watResolver->batchResolve($activityTypes);
        // $activityIsToleratedMap = $this->isToleratedResolver->batchResolve($activityIsTolerated);

        $maps = [
            'activityTypes' => $activityTypeMap,
            // 'activityIsTolerated' => $activityIsToleratedMap
        ];

        foreach ($context as $ctx) {
            $result[] = $this->resolve($ctx, $maps);
        }

        return $result;
    }

}
