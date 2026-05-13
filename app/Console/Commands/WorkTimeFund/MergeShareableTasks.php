<?php

namespace App\Console\Commands\WorkTimeFund;

use App\Console\Services\BatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @TODO
 */
class MergeShareableTasks extends Command
{

    protected $signature = 'wtf:merge-shareable-tasks
        {--categoryId= : The ID of the category}';

    protected $description = 'Merge tasks created as standalone to pose as shared';

    public function handle()
    {
        $categoryId = $this->option('categoryId');
        if ($categoryId === null) {
            $this->error('category_id option is required.');
            return 1;
        }

        if (!ctype_digit($categoryId)) {
            $this->error('category_id must be an integer.');
            return 1;
        }

        $categoryId = (int) $categoryId;

        $this->info("Split start");

        $connection = DB::connection(); // lock default connection

        $this->info("Split running ...");
        $this->prepareTempTables($connection, $categoryId);
        $this->splitTasks($connection);
        $this->splitWorkorders($connection);
        $this->isValid($connection);
        $this->cleanUp($connection);
        // try {
        //     $connection->transaction(function () use ($connection) {
        //     });
        // } catch (\Throwable $e) {
        //     $this->error('Transaction failed: ' . $e->getMessage());
        // }
        $this->info("Split finished");

        return 0;
    }

    private function prepareTempTables($db, int $rootCategoryId)
    {
        $this->info("Prepare temp structures and data");

        // filter relevant tasks that should be split
        $sql = "
            CREATE TEMPORARY TABLE tmp_shareable_tasks (
                task_id BIGINT UNSIGNED PRIMARY KEY
            )
        ";
        $db->statement($sql);

        $sql = "
            WITH RECURSIVE category_tree AS (
                -- start from root category
                SELECT id
                FROM dpb_worktimefund_model_category
                WHERE id = :rootCategoryId

                UNION ALL

                -- get all children recursively
                SELECT c.id
                FROM dpb_worktimefund_model_category c
                JOIN category_tree ct
                    ON c.parent_id = ct.id
            )        
            INSERT INTO tmp_shareable_tasks (task_id)
            SELECT t.id
            FROM dpb_worktimefund_model_task t
            JOIN dpb_worktimefund_model_operation o 
                ON o.id = t.source_id
            JOIN category_tree ct
                ON o.parent_id = ct.id
            WHERE o.is_shareable = 1                
        ";
        $db->statement($sql, [
            'rootCategoryId' => $rootCategoryId,            
        ]);

        // create temporary mapping table
        $sql = "
            CREATE TEMPORARY TABLE tmp_task_activity_map (
                activity_id BIGINT UNSIGNED PRIMARY KEY,
                old_task_id BIGINT UNSIGNED,
                new_task_id BIGINT UNSIGNED NULL
            )
        ";
        $db->statement($sql);

        // fill mapping table one row per activity linked to a shareable task.
        $sql = "
            INSERT INTO tmp_task_activity_map (
                activity_id,
                old_task_id
            )
            SELECT
                ar.id,
                t.id
            FROM dpb_worktimefund_model_activityrecord ar
            JOIN tmp_shareable_tasks t
                ON t.task_id = ar.task_id
        ";
        $db->statement($sql);

        // add temporary migration UUID column
        $sql = "
            ALTER TABLE dpb_worktimefund_model_task
            ADD COLUMN migration_activity_id BIGINT UNSIGNED NULL       
        ";
        $db->statement($sql);
    }

    private function splitTasks($db)
    {
        $this->info("Split tasks");
        // CLONE tasks
        // every cloned task knows which activity it belongs to.
        // INSERT INTO dpb_worktimefund_model_task (
        //     source_id,
        //     title,
        //     expected_duration,
        //     is_shareable,
        //     status,
        //     department_id,
        //     created_at,
        //     updated_at,
        //     maintainable_type,
        //     maintainable_id,
        //     migration_activity_id
        // )
        // SELECT
        //     t.source_id,
        //     t.title,
        //     t.expected_duration,
        //     0,
        //     t.status,
        //     t.department_id,
        //     NOW(),
        //     NOW(),
        //     t.maintainable_type,
        //     t.maintainable_id,
        //     ar.id
        // FROM dpb_worktimefund_model_activityrecord ar
        // JOIN tmp_shareable_tasks t
        //     ON t.task_id = ar.task_id;

        // -- populate new_task_id mapping
        // UPDATE tmp_task_activity_map m
        // JOIN dpb_worktimefund_model_task t
        //     ON t.migration_activity_id = m.activity_id
        // SET m.new_task_id = t.id;

        // -- make every activity point to its own dedicated task.
        // UPDATE dpb_worktimefund_model_activityrecord ar
        // JOIN tmp_task_activity_map m
        //     ON m.activity_id = ar.id
        // SET ar.task_id = m.new_task_id;
    }

    private function splitWorkorders($db)
    {
        $this->info("Split workorders");
        // -- CLONE tasks
        // -- every cloned task knows which activity it belongs to.
        // INSERT INTO dpb_worktimefund_model_task (
        //     source_id,
        //     title,
        //     expected_duration,
        //     is_shareable,
        //     status,
        //     department_id,
        //     created_at,
        //     updated_at,
        //     maintainable_type,
        //     maintainable_id,
        //     migration_activity_id
        // )
        // SELECT
        //     t.source_id,
        //     t.title,
        //     t.expected_duration,
        //     0,
        //     t.status,
        //     t.department_id,
        //     NOW(),
        //     NOW(),
        //     t.maintainable_type,
        //     t.maintainable_id,
        //     ar.id
        // FROM dpb_worktimefund_model_activityrecord ar
        // JOIN dpb_worktimefund_model_task t
        //     ON t.id = ar.task_id
        // WHERE t.is_shareable = 1;

        // -- populate new_task_id mapping
        // UPDATE tmp_task_activity_map m
        // JOIN dpb_worktimefund_model_task t
        //     ON t.migration_activity_id = m.activity_id
        // SET m.new_task_id = t.id;

        // -- make every activity point to its own dedicated task.
        // UPDATE dpb_worktimefund_model_activityrecord ar
        // JOIN tmp_task_activity_map m
        //     ON m.activity_id = ar.id
        // SET ar.task_id = m.new_task_id;
    }

    private function isValid($db): bool
    {

        $this->info("Validate after split");
        // -- Should return no rows for migrated shareable tasks.
        // SELECT task_id, COUNT(*)
        // FROM dpb_worktimefund_model_activityrecord
        // GROUP BY task_id
        // HAVING COUNT(*) > 1;

        // -- Should equal number of inserted cloned tasks.
        // SELECT COUNT(*)
        // FROM dpb_worktimefund_model_activityrecord ar
        // JOIN dpb_worktimefund_model_task t
        //     ON t.id = ar.task_id     
        // WHERE t.is_shareable = 1;       
        return true;
    }

    private function cleanUp($db)
    {

        $this->info("Clean up temporary data and structures");
        /*
DELETE t
FROM dpb_worktimefund_model_task t
WHERE t.is_shareable = 1
AND EXISTS (
    SELECT 1
    FROM tmp_task_activity_map m
    WHERE m.old_task_id = t.id
);
*/
        // add temporary migration UUID column
        $sql = "
            ALTER TABLE dpb_worktimefund_model_task
            DROP COLUMN migration_activity_id;        
        ";
        $db->statement($sql);
    }
}
