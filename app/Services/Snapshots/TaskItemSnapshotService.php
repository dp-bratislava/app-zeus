<?php

namespace App\Services\Snapshots;

use App\Resolvers\Snapshots\TaskItemSnapshotResolver;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\TaskMS\Models\TaskItemAssignment;
use Illuminate\Support\Facades\DB;

class TaskItemSnapshotService
{
    public function __construct(
        public array $taskItemIds
    ) {}

    public function handle(): void
    {
        // insert raw data
        DB::statement("CREATE TEMPORARY TABLE tmp_task_item_ids (id BIGINT PRIMARY KEY)");

        DB::table('tmp_task_item_ids')->insert(
            array_map(fn($id) => ['id' => $id], $this->taskItemIds)
        );

        DB::statement($this->query());

        DB::statement("DROP TEMPORARY TABLE tmp_task_item_ids");

        // resolve polymorphic data
        // $activities = DB::table('dpb_worktimefund_model_activityrecord as ar')
        //     ->leftJoin('dpb_worktimefund_model_task as t', 't.id', '=', 'ar.task_id')
        //     // ->leftJoin('dpb_worktimefund_mm_morphable_attributeoption as mato', 'mato.attributeoption_id', '=', 'ato.id')
        //     // ->leftJoin('dpb_worktimefund_model_attributeoption as ato', 'att.id', '=', 'ar.task_id')
        //     // ->leftJoin('dpb_worktimefund_model_attributetype as att', 'att.id', '=', 'ato.attributetype_id')

        //     ->select([
        //         'ar.id',
        //         't.id as task_id',
        //         't.maintainable_type',
        //         't.maintainable_id',
        //     ])
        //     ->whereIn('ar.id', $this->activityIds)
        //     ->get();

        // $wtsResolver = app(WorkTaskSubjectResolver::class);
        // $subjects = $wtsResolver->batchResolve($activities);
        // // dd($subjects);
        // DB::table('mvw_work_activity_report')->upsert(
        //     $subjects,
        //     ['activity_id'],
        //     ['subject_type', 'subject_label']
        // );
    }

    protected function query(): string
    {
        return $sql = "
INSERT INTO mvw_task_item_snapshots (
            task_item_id,
            -- task
            task_id,
            task_date,
            task_title,
            task_description,
            task_group_title,
            task_assigned_to_type,
            task_assigned_to_label,
            task_requested_for_type,
            task_requested_for_label,
            task_author_lastname,
            task_place_of_origin,
            task_created_at,

            -- task item 
            task_item_date,
            task_item_title,
            task_item_description,
            task_item_group_title,
            task_item_assigned_to,
            task_item_author_lastname,
            task_item_created_at,

            updated_at
)
SELECT
            task_item_id,
            
            -- task
            t.id,
            t.date,
            t.title,
            t.description,
            tg.title,
            NULL, -- task_assigned_to_type,
            NULL, -- task_assigned_to_label,
            NULL, -- task_requested_for_type,
            NULL, -- task_requested_for_label,            
            tu.lastname,
            po.title, -- task_place_of_origin,
            t.created_at,

            -- task item 
            ti.date,
            ti.title,
            ti.description,
            tig.title,
            NULL, -- task_item_assigned_to_type,
            NULL, -- task_item_assigned_to_label,
            tiu.lastname,
            
            ti.created_at,
            tia.updated_at
FROM tms_task_item_assignments tia

JOIN tmp_task_item_ids tmp
    ON tmp.id = tia.id
    LEFT JOIN tsk_task_items ti ON tia.task_item_id = ti.id
    LEFT JOIN tsk_task_item_groups tig ON tig.id = ti.group_id
    LEFT JOIN users tiu ON tiu.id = tia.author_id
    LEFT JOIN tms_task_assignments ta ON ta.task_id = ti.task_id
    LEFT JOIN tsk_tasks t ON t.id = ta.task_id
    LEFT JOIN tsk_task_groups tg ON tg.id = t.group_id
    LEFT JOIN users tu ON tu.id = ta.author_id
    LEFT JOIN tsk_places_of_origin po ON po.id = t.place_of_origin_id
ON DUPLICATE KEY UPDATE
    updated_at = VALUES(updated_at)
        ";
    }


    public function handle2(): void
    {
        $baseQuery = TaskItemAssignment::whereIn('task_item_id', $this->taskItemIds);

        // Step 1: lightweight ID extraction
        $taskIds = (clone $baseQuery)
            ->join('tsk_task_items', 'tsk_task_items.id', '=', 'tms_task_item_assignments.task_item_id')
            ->pluck('tsk_task_items.task_id')
            ->unique();

        $tias = $baseQuery
            ->with([
                'assignedTo',
                'taskItem',
                'taskItem.group',
                'taskItem.task',
                'taskItem.task.group',
                'taskItem.task.placeOfOrigin',
                'author:id,lastname',
                // 'assignments.maintenanceGroup',
            ])
            ->get()
            ->keyBy('id');

        $taskAssignments = TaskAssignment::whereIn('task_id', $taskIds)
            ->with([
                'assignedTo',
                'task',
                'task.group',
                'task.placeOfOrigin',
                'author:id,lastname',
            ])
            ->get()
            ->keyBy('id');

        $taskItemResolver = app(TaskItemSnapshotResolver::class);
        $taskSnapshots = $taskItemResolver->batchResolve($tias, $taskAssignments);

        // dd($taskSnapshots);
        DB::table('mvw_task_item_snapshots')->upsert(
            $taskSnapshots,
            ['task_id'],
            // ['task_item_date', 'task_item_title', 'create_at', 'updated_at']
        );
    }

    public function handle1(): void
    {
        $baseQuery = TaskItemAssignment::whereIn('task_item_id', $this->taskItemIds);

        // Step 1: lightweight ID extraction
        // $taskIds = (clone $baseQuery)
        //     ->join('tsk_task_items', 'task_items.id', '=', 'tms_task_item_assignments.task_item_id')
        //     ->pluck('tsk_task_items.task_id')
        //     ->unique();

        $tias = $baseQuery
            ->with([
                'assignedTo',
                'taskItem',
                'taskItem.group',
                'taskItem.task',
                'taskItem.task.group',
                'taskItem.task.placeOfOrigin',
                'author:id,lastname',
                // 'assignments.maintenanceGroup',
            ])
            ->get()
            ->keyBy('id');

        // $tas = TaskAssignment::whereIn('task_id', $taskIds)
        //     ->with([
        //         'task',
        //         'task.group',
        //         'task.placeOfOrigin',
        //         'author:id,lastname',
        //         // 'assignments.maintenanceGroup',
        //     ])
        //     ->get()
        //     ->keyBy('id');

        $taskItemResolver = app(TaskItemSnapshotResolver::class);
        $taskSnapshots = $taskItemResolver->batchResolve($tias);

        // dd($taskSnapshots);
        DB::table('mvw_task_item_snapshots')->upsert(
            $taskSnapshots,
            ['task_id'],
            // ['task_item_date', 'task_item_title', 'create_at', 'updated_at']
        );
    }
}
