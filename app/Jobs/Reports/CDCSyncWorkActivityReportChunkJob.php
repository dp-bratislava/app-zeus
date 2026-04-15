<?php

namespace App\Jobs\Reports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class SyncWorkActivityReportChunkJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public array $activityIds
    ) {}

    public function handle(): void
    {
        DB::statement("CREATE TEMPORARY TABLE tmp_activity_ids (id BIGINT PRIMARY KEY)");

        DB::table('tmp_activity_ids')->insert(
            array_map(fn($id) => ['id' => $id], $this->activityIds)
        );

        DB::statement($this->query());

        DB::statement("DROP TEMPORARY TABLE tmp_activity_ids");
    }

    protected function query(): string
    {
        return $sql = "
INSERT INTO mvw_work_activity_report (
    activity_id,
    department_id,
    department_code,
    task_id,
    task_created_at,
    task_date,
    subject_code,
    task_group_title,
    task_maintenance_group,
    task_maintenance_group_code,
    task_author_lastname,
    task_item_group_title,
    task_item_maintenance_group,
    task_item_maintenance_group_code,
    task_item_author_lastname,
    wtf_task_created_at,
    activity_date,
    personal_id,
    last_name,
    first_name,
    wtf_task_title,
    expected_duration,
    real_duration,
    is_fulfilled,
    source_deleted_at,
    source_updated_at
)
SELECT
    ar.id,
    d.id,
    d.code,
    tsk_tasks.id,
    ta.created_at,
    DATE(tsk_tasks.date),
    vc.code,
    tsk_task_groups.title,
    mgta.title,
    mgta.code,
    uta.lastname,
    tig.title,
    mgtia.title,
    mgtia.code,
    utia.lastname,
    wtf_task.created_at,
    DATE(ar.date),
    wt.personal_id,
    wt.last_name,
    wt.first_name,
    ar.title,
    ar.expected_duration,
    ar.real_duration,
    ar.is_fulfilled,
    ar.deleted_at,
    ar.updated_at
FROM dpb_worktimefund_model_activityrecord ar

JOIN tmp_activity_ids tmp
    ON tmp.id = ar.id

LEFT JOIN dpb_worktimefund_model_worktime wt ON wt.id = ar.parent_id
LEFT JOIN datahub_departments d ON d.id = ar.department_id
LEFT JOIN dpb_worktimefund_model_task wtf_task ON ar.task_id = wtf_task.id
LEFT JOIN dpb_wtftmsbridge_mm_workorder_task wot ON wot.taskitem_id = wtf_task.id
LEFT JOIN dpb_wtftmsbridge_model_workorder wo ON wo.id = wot.workorder_id
LEFT JOIN tsk_task_items ti ON wo.tms_task_item_id = ti.id
LEFT JOIN tsk_task_item_groups tig ON tig.id = ti.group_id
LEFT JOIN tsk_tasks ON tsk_tasks.id = ti.task_id
LEFT JOIN tsk_task_groups ON tsk_task_groups.id = tsk_tasks.group_id
LEFT JOIN tms_task_assignments ta ON ta.task_id = tsk_tasks.id
LEFT JOIN tms_task_item_assignments tia ON tia.task_item_id = ti.id
LEFT JOIN users uta ON ta.author_id = uta.id
LEFT JOIN users utia ON tia.author_id = utia.id
LEFT JOIN fleet_vehicle_code_history vch 
    ON (vch.vehicle_id = ta.subject_id AND vch.date_to IS NULL AND ta.subject_type = 'vehicle')
LEFT JOIN fleet_vehicle_codes vc ON vc.id = vch.vehicle_code_id
LEFT JOIN fleet_maintenance_groups mgta 
    ON (ta.assigned_to_id = mgta.id AND ta.assigned_to_type = 'maintenance-group')
LEFT JOIN fleet_maintenance_groups mgtia 
    ON (tia.assigned_to_id = mgtia.id AND tia.assigned_to_type = 'maintenance-group')
ON DUPLICATE KEY UPDATE
    real_duration = VALUES(real_duration),
    is_fulfilled = VALUES(is_fulfilled),
    source_deleted_at = VALUES(source_deleted_at),
    source_updated_at = VALUES(source_updated_at);
        ";
    }
}
