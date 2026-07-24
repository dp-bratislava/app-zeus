<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP view if EXISTS `vw_dpb_wtf_operations_v2`');
        DB::statement("
            CREATE view `vw_dpb_wtf_operations_v2` AS
            WITH RECURSIVE category_path AS (
                -- Start from each operation's category
                SELECT
                    o.id AS operation_id,
                    c.id,
                    c.parent_id,
                    c.title,
                    0 AS depth
                FROM dpb_worktimefund_model_operation o
                JOIN dpb_worktimefund_model_category c
                    ON c.id = o.parent_id

                UNION ALL

                -- Walk towards the root
                SELECT
                    cp.operation_id,
                    p.id,
                    p.parent_id,
                    p.title,
                    cp.depth + 1
                FROM category_path cp
                JOIN dpb_worktimefund_model_category p
                    ON p.id = cp.parent_id
            )
            SELECT
                o.id,
                o.title AS operation,
                o.duration / 60 AS duration_minutes,
                o.is_official,
                leaf.title AS category,
                root.title AS root_category,
                d.`code` AS department_code,
                d.title AS department
            FROM category_path root
            JOIN dpb_worktimefund_model_operation o
                ON o.id = root.operation_id
            JOIN dpb_worktimefund_model_category leaf
                ON leaf.id = o.parent_id
            JOIN dpb_departments_mm_morphable_department cd
                ON cd.morphable_id = root.id
                AND morphable_type = 'Dpb\\\\WorkTimeFund\\\\Models\\\\Category'
            JOIN datahub_departments d
                ON d.id = cd.department_id
            WHERE 
                root.parent_id IS NULL
                and o.deleted_at IS null
                AND leaf.deleted_at IS null
            ORDER BY
                root.title,
                leaf.title,
                o.title,
                d.`code`,
                d.title 
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP view if EXISTS `vw_dpb_wtf_operations_v2`');
    }
};
