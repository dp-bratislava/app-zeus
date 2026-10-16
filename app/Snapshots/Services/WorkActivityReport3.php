<?php

namespace App\Snapshots\Services;

use App\Snapshots\Core\Contracts\SnapshotExecutionStrategy;
use App\Snapshots\Core\Snapshot;
use App\Snapshots\Core\SnapshotExecutionState;
use App\Snapshots\Core\SnapshotRunContext;
use App\Snapshots\Strategies\TempTableStrategy;
use Illuminate\Support\Facades\DB;

class WorkActivityReport3 extends Snapshot
{
    public function getKey(): string
    {
        return 'work-activity-v3';
    }

    public function targetTable(): string
    {
        return 'mvw_work_activity_report_v3';
    }

    public function sourceTable(): string
    {
        // v3 reads straight from the v2 snapshot.
        return 'mvw_work_activity_report_v2';
    }

    public function strategy(): SnapshotExecutionStrategy
    {
        return app(TempTableStrategy::class);
    }

    public function idQuery(SnapshotRunContext $context)
    {
        // v2 uses dedicated audit columns rather than updated_at/deleted_at.
        return DB::table($this->sourceTable())
            ->where('source_updated_at', '>', $context->from)
            ->orWhere('source_deleted_at', '>', $context->from)
            ->select('activity_id as id');
    }

    public function run(SnapshotRunContext $context, SnapshotExecutionState $state): void
    {
        DB::statement(
            $this->buildInsertSql(
                $state->tempTable,
            )
        );
    }

    public function buildInsertSql(string $tempTable): string
    {
        return "
            INSERT INTO {$this->targetTable()}
                ({$this->columns()})
            SELECT
                {$this->select()}
            FROM
                {$this->from($tempTable)}
            ON DUPLICATE KEY UPDATE
                {$this->duplicateKeyUpdate('activity_id')}
            ";
    }

    protected function map(): array
    {
        // 1:1 copy of v2 columns. The joining alias is `report`.
        return [
            // activity
            'activity_id' => 'report.activity_id',

            'personal_id' => 'report.personal_id',
            'last_name' => 'report.last_name',
            'first_name' => 'report.first_name',
            'department_code' => 'report.department_code',

            'activity_date' => 'report.activity_date',
            'activity_title' => 'report.activity_title',
            'activity_expected_duration' => 'report.activity_expected_duration',
            'activity_real_duration' => 'report.activity_real_duration',
            'activity_is_fulfilled' => 'report.activity_is_fulfilled',
            'activity_is_fulfilled_label' => 'report.activity_is_fulfilled_label',
            'activity_type' => 'report.activity_type',
            'activity_is_tolerated' => 'report.activity_is_tolerated',

            // sap codes
            'group_sap_code' => 'report.group_sap_code',
            'activity_sap_code' => 'report.activity_sap_code',

            // task snapshot
            'task_date' => 'report.task_date',
            'task_group_title' => 'report.task_group_title',
            'task_assigned_to_type' => 'report.task_assigned_to_type',
            'task_assigned_to_label' => 'report.task_assigned_to_label',
            'task_requested_for_type' => 'report.task_requested_for_type',
            'task_requested_for_label' => 'report.task_requested_for_label',
            'task_author_lastname' => 'report.task_author_lastname',

            // task item snapshot
            'task_item_group_title' => 'report.task_item_group_title',
            'task_item_assigned_to_type' => 'report.task_item_assigned_to_type',
            'task_item_assigned_to_label' => 'report.task_item_assigned_to_label',
            'task_item_author_lastname' => 'report.task_item_author_lastname',

            // ids
            'task_id' => 'report.task_id',
            'task_created_at' => 'report.task_created_at',
            'task_item_id' => 'report.task_item_id',
            'wtf_task_id' => 'report.wtf_task_id',
            'wtf_task_created_at' => 'report.wtf_task_created_at',

            // missing join placeholder
            'department_id' => 'report.department_id',

            // audit
            'source_deleted_at' => 'report.source_deleted_at',
            'source_updated_at' => 'report.source_updated_at',

            // v3-only subject columns, brought in from mvw_work_task_subject_snapshots.
            // Every v3 row has exactly one corresponding subject (1:1 join).
            'subject_type' => 'wtss.subject_type',
            'subject_label' => 'wtss.subject_label',
            // TODO: fill department yourself later.
            'subject_department' => 'NULL',
        ];
    }

    protected function from(string $tempTable): string
    {
        return "
            mvw_work_activity_report_v2 report
            JOIN {$tempTable} tmp ON tmp.id = report.activity_id
            LEFT JOIN mvw_work_task_subject_snapshots wtss ON wtss.wtf_task_id = report.wtf_task_id
        ";
    }

    /**
     * @TODO v2 snapshot has no `type`/`task_id` columns; revisit for v3.
     */
    public function polymorphicContext(array $activityIds = []): string
    {
        $base = "
            SELECT
                report.activity_id as activity_id,
                report.activity_type as activity_type,
                report.task_id as task_id
            FROM mvw_work_activity_report_v2 report
        ";

        if (!empty($activityIds)) {
            $base .= " WHERE report.activity_id IN (" . implode(',', $activityIds) . ")";
        }

        return $base;
    }
}