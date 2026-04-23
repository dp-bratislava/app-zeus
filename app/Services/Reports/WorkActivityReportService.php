<?php

namespace App\Services\Reports;

use App\Registries\Reports\WorkActivityReportSQLRegistry;
use App\Resolvers\Reports\WorkActivityReportResolver;
use Illuminate\Support\Facades\DB;

class WorkActivityReportService
{
    public function __construct(
        public WorkActivityReportSQLRegistry $sqlRegistry,
        public WorkActivityReportResolver $polymorphicsResolver
    ) {}

    public function handle(array $activityIds): void
    {
        // insert raw data
        $this->createTemporaryTables();

        DB::table('tmp_activity_record_ids')->insert(
            array_map(fn($id) => ['id' => $id], $activityIds)
        );

// dd($this->sqlRegistry->build());

        DB::statement($this->sqlRegistry->build());

        $this->dropTemporaryTables();

        // resolve polymorphic data
        // collect($taskItemIds)
        //     ->chunk(2000)
        //     ->each(function ($chunk) {
        //         $this->resolvePolymorphics($chunk);
        //     });
    }

    protected function createTemporaryTables()
    {
        DB::statement("CREATE TEMPORARY TABLE tmp_activity_record_ids (id BIGINT PRIMARY KEY)");
    }

    protected function dropTemporaryTables()
    {
        DB::statement("DROP TEMPORARY TABLE tmp_activity_record_ids");
    }

    protected function resolvePolymorphics($taskItemIds)
    {
        $context = DB::table(DB::raw("({$this->sqlRegistry->polymorphicContext($taskItemIds->toArray())}) as ctx"))
            ->get();

        if ($context->isEmpty()) {
            return;
        }

        $arRelations = $this->polymorphicsResolver->batchResolve($context);

        // dd($taskSnapshots);
        DB::table('mvw_work_activity_report')->upsert(
            $arRelations,
            ['activity_id'],
            // ['task_item_date', 'task_item_title', 'create_at', 'updated_at']
        );
    }

    public function handle1(): void
    {
        // $activities = ActivityRecord::whereIn('id', $this->activityIds)
        //     ->with([
        //         'task.maintainable', 
        //         'task.attributeOptions', 
        //         'task.attributeOptions.type'
        //         ])
        //     ->get()
        //     ->keyBy('id');            

        $activities = DB::table('dpb_worktimefund_model_activityrecord as ar')
            ->leftJoin('dpb_worktimefund_model_task as t', 't.id', '=', 'ar.task_id')
            ->leftJoin('dpb_wtftmsbridge_mm_workorder_task as wot', 'wot.taskitem_id', '=', 't.id')
            ->leftJoin('mvw_task_item_snapshots as tis', 'wot.taskitem_id', '=', 'tis.task_item_id')
            ->leftJoin('mvw_hr_contract_snapshots as hrc', 'hrc.pid', '=', 'ar.personal_id')
            ->select([
                'ar.id',
                'ar.date',
                'ar.task_id as wtf_task_id',
                'ar.title',
                'ar.expected_duration',
                'ar.real_duration',
                'ar.is_fulfilled',
                'ar.deleted_at',
                'ar.updated_at',
                't.created_at as wtf_task_created_at',
                't.title as wtf_task_title',
                't.maintainable_type',
                't.maintainable_id',
                'tis.task_created_at',
                'tis.task_date',
                'tis.task_group_title',
                'tis.task_assigned_to',
                'tis.task_author_lastname',
                'tis.task_item_group_title',
                'tis.task_item_assigned_to',
                'tis.task_item_author_lastname',
                'tis.task_id',
                'tis.task_item_id',
                'hrc.pid',
                'hrc.last_name',
                'hrc.first_name',
                'hrc.department_code',
            ])
            ->whereIn('ar.id', $this->activityIds)
            ->get();

        //   dd($workTaskSubjects[0]) ;          
        //   exit;
        $warResolver = app(WorkActivityReportResolver::class);
        $workActivities = $warResolver->batchResolve($activities);

        // dd($taskSnapshots);
        DB::table('mvw_work_activity_report')->upsert(
            $workActivities,
            ['activity_id'],
            // ['task_item_date', 'task_item_title', 'create_at', 'updated_at']
        );
    }
}
