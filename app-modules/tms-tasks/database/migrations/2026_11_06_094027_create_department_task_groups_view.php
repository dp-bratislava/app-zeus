
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $viewPrefix = 'vw_' . config('pkg-task-ms.table_prefix') . 'task_batch_';
  
        $sql = <<<SQL
            CREATE VIEW {$viewPrefix}department_task_groups AS
            SELECT
                dg.department_id,
                dg.task_group_id,
                tg.title AS task_group_label,
                dg.`mode`
            FROM tms_task_batch_department_task_group dg
            INNER JOIN tsk_task_groups tg
                ON tg.id = dg.task_group_id
        SQL;

        DB::statement($sql);        
    }

    public function down(): void
    {
        $viewPrefix = 'vw_' . config('pkg-task-ms.table_prefix') . 'task_batch_';

        DB::statement(
            "DROP VIEW IF EXISTS " . $viewPrefix . "department_task_groups"
        );
    }
};
