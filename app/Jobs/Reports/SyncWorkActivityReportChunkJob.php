<?php

namespace App\Jobs\Reports;

use App\Resolvers\Reports\WorkTaskSubjectResolver;
use App\Services\Reports\WorkActivityReportService;
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
        app(WorkActivityReportService::class)->handle($this->activityIds);
    }

    public function handle1(): void
    {
        // insert raw data
        DB::statement("CREATE TEMPORARY TABLE tmp_activity_ids (id BIGINT PRIMARY KEY)");

        DB::table('tmp_activity_ids')->insert(
            array_map(fn($id) => ['id' => $id], $this->activityIds)
        );

        DB::statement($this->query());

        DB::statement("DROP TEMPORARY TABLE tmp_activity_ids");

        // resolve polymorphic data
        $activities = DB::table('dpb_worktimefund_model_activityrecord as ar')
            ->leftJoin('dpb_worktimefund_model_task as t', 't.id', '=', 'ar.task_id')
            // ->leftJoin('dpb_worktimefund_mm_morphable_attributeoption as mato', 'mato.attributeoption_id', '=', 'ato.id')
            // ->leftJoin('dpb_worktimefund_model_attributeoption as ato', 'att.id', '=', 'ar.task_id')
            // ->leftJoin('dpb_worktimefund_model_attributetype as att', 'att.id', '=', 'ato.attributetype_id')

            ->select([
                'ar.id',
                't.id as task_id',
                't.maintainable_type',
                't.maintainable_id',
            ])
            ->whereIn('ar.id', $this->activityIds)
            ->get();

        $wtsResolver = app(WorkTaskSubjectResolver::class);
        $subjects = $wtsResolver->batchResolve($activities);
// dd($subjects);
        DB::table('mvw_work_activity_report')->upsert(
            $subjects,
            ['activity_id'],
            ['subject_type', 'subject_label']
        );
    }

    protected function query(): string
    {
        return $sql = "
INSERT INTO mvw_work_activity_report (
    activity_id,
    department_id,
    department_code,
    task_created_at,
    task_date,
    subject_type,
    subject_label,
    task_group_title,
    task_assigned_to,
    task_author_lastname,
    task_item_group_title,
    task_item_assigned_to,
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
    task_id,
    task_item_id,
    source_deleted_at,
    source_updated_at
)
SELECT
    ar.id,
    NULL, -- hrc.department_id,
    hrc.department_code,
    tis.task_created_at,
    DATE(tis.task_date),
    NULL, -- subject_type,
    NULL, -- subject_label,
    tis.task_group_title,
    tis.task_assigned_to,
    tis.task_author_lastname,
    tis.task_item_group_title,
    tis.task_item_assigned_to,
    tis.task_item_author_lastname,
    wtf_task.created_at,
    DATE(ar.date),
    ar.personal_id,
    hrc.last_name,
    hrc.first_name,
    ar.title,
    ar.expected_duration,
    ar.real_duration,
    ar.is_fulfilled,
    tis.task_id,
    tis.task_item_id,
    ar.deleted_at,
    ar.updated_at
FROM dpb_worktimefund_model_activityrecord ar

JOIN tmp_activity_ids tmp
    ON tmp.id = ar.id

LEFT JOIN dpb_worktimefund_model_task wtf_task ON ar.task_id = wtf_task.id
LEFT JOIN dpb_wtftmsbridge_mm_workorder_task wot ON wot.taskitem_id = wtf_task.id
LEFT JOIN dpb_wtftmsbridge_model_workorder wo ON wo.id = wot.workorder_id

LEFT JOIN mvw_task_item_snapshots tis ON tis.task_item_id = wo.tms_task_item_id
LEFT JOIN mvw_hr_contract_snapshots hrc ON ar.personal_id = hrc.pid

-- LEFT JOIN fleet_vehicle_code_history vch 
--    ON (vch.vehicle_id = ta.subject_id AND vch.date_to IS NULL AND ta.subject_type = 'vehicle')
-- LEFT JOIN fleet_vehicle_codes vc ON vc.id = vch.vehicle_code_id
ON DUPLICATE KEY UPDATE
    real_duration = VALUES(real_duration),
    is_fulfilled = VALUES(is_fulfilled),
    source_deleted_at = VALUES(source_deleted_at),
    source_updated_at = VALUES(source_updated_at);
        ";
    }
    protected function query1(): string
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
