<?php

namespace App\Resolvers\Reports;

use Dpb\WorkTimeFund\Models\Maintainables\Table;
use Dpb\WorkTimeFund\Models\Maintainables\Vehicle;

class WorkActivityReportResolver
{
    private $vehicles = null;
    private $tables = null;

    public function __construct(
        protected WorkTaskSubjectResolver $wtsResolver,
    ) {}

    public function resolve1($activity): array
    {
        // dd($activity);
        // $subject = $this->wtsResolver->resolve($activity->task);
        return [
            'activity_id' => $activity->id,
            //    'department_id' => ,
            //    'department_code' => ,
            //    'task_created_at' => ,
            //    'task_date' => ,
            // 'subject_type' => $subject !== null ? $subject['type'] : null,
            // 'subject_label' => $subject !== null ? $subject['label'] : null,
            //    'task_group_title' => ,
            //    'task_assigned_to' => ,
            //    'task_author_lastname' => ,
            //    'task_item_group_title' => ,
            //    'task_item_assigned_to' => ,
            //    'task_item_author_lastname' => ,
            //    'wtf_task_created_at' => ,
            //    'activity_date' => ,
            //    'personal_id' => ,
            //    'last_name' => ,
            //    'first_name' => ,
            //    'wtf_task_title' => ,
            'expected_duration' => $activity->expected_duration,
            'real_duration' => $activity->real_duration,
            'is_fulfilled' => $activity->is_fulfilled,
            //    'task_id' => $activity->,
            //    'task_item_id' => ,
            'source_deleted_at' => $activity->deleted_at,
            'source_updated_at' => $activity->updated_at,
        ];
    }

    public function resolve($activity): array
    {
        // dd($activity);
        // $subject = $this->wtsResolver->resolve($activity->task);
        return [
            'activity_id' => $activity->id,
            //    'department_id' => ,
            'department_code' => $activity->department_code,
            'task_created_at' => $activity->task_created_at,
            'task_date' => $activity->task_date,
            // 'subject_type' => $subject !== null ? $subject['type'] : null,
            // 'subject_label' => $subject !== null ? $subject['label'] : null,
            'task_group_title' => $activity->task_group_title,
            'task_assigned_to' => $activity->task_assigned_to,
            'task_author_lastname' => $activity->task_author_lastname,
            'task_item_group_title' => $activity->task_item_group_title,
            'task_item_assigned_to' => $activity->task_item_assigned_to,
            'task_item_author_lastname' => $activity->task_item_author_lastname,
            'wtf_task_created_at' => $activity->wtf_task_created_at,
            'activity_date' => $activity->date,
            'personal_id' => $activity->pid,
            'last_name' => $activity->last_name,
            'first_name' => $activity->first_name,
            'wtf_task_title' => $activity->wtf_task_title,
            'expected_duration' => $activity->expected_duration,
            'real_duration' => $activity->real_duration,
            'is_fulfilled' => $activity->is_fulfilled,
            'task_id' => $activity->task_id,
            'task_item_id' => $activity->task_item_id,
            'source_deleted_at' => $activity->deleted_at,
            'source_updated_at' => $activity->updated_at,
        ];
    }

    public function batchResolve($activities)
    {
        $result = [];

        if (empty($activities)) {
            return $result;
        }

        // vehicles
        $vehicleIds = $activities->where('maintainable_type', Vehicle::class)->pluck('maintainable_id');

        $this->vehicles = Vehicle::whereIn('id', $vehicleIds)
            ->with(['code']) // ONLY what resolver needs
            ->get()
            ->keyBy('id');

        // tables
        $tableIds = $activities->where('maintainable_type', Table::class)->pluck('maintainable_id');
        $this->tables = Table::whereIn('id', $tableIds)
            ->with(['location']) // ONLY what resolver needs
            ->get()
            ->keyBy('id');

        foreach ($activities as $activity) {
            $result[] = $this->resolve($activity);
        }

        return $result;
    }
}
