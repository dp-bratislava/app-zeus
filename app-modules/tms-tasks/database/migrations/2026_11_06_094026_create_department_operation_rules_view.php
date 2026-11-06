
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $viewPrefix = 'vw_' . config('pkg-task-ms.table_prefix') . 'task_batch_';

        $sql = <<<SQL
            CREATE VIEW {$viewPrefix}department_operation_rules AS
            SELECT
                dg.department_id,
                dg.task_group_id,
                dgi.task_item_group_id,
                dor.operation_id,
                dor.subject_type,
                dor.conditions
            FROM tms_task_batch_department_task_group dg
            INNER JOIN tms_task_batch_department_task_group_items dgi
                ON dgi.department_task_group_id = dg.id
            INNER JOIN tms_task_batch_department_operation_rules dor
                ON dor.department_task_group_item_id = dgi.id
        SQL;

        DB::statement($sql);
    }

    public function down(): void
    {
        $viewPrefix = 'vw_' . config('pkg-task-ms.table_prefix') . 'task_batch_';

        DB::statement(
            "DROP VIEW IF EXISTS " . $viewPrefix . "department_operation_rules"
        );
    }
};
