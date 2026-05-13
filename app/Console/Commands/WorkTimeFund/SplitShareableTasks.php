<?php

namespace App\Console\Commands\WorkTimeFund;

use App\Console\Services\BatchService;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SplitShareableTasks extends Command
{

    protected $signature = 'wtf:convert:shareable-to-unshareable
        {--categoryId= : The ID of the category}
        {--dateFrom= : The ID of the category}
        {--dateTo= : The ID of the category}';

    protected $description = 'Split tasks created as shareable to pose as standalone';

    private function inputIsValid(): bool
    {
        $categoryId = $this->option('categoryId');
        if ($categoryId === null) {
            $this->error('category_id option is required.');
            return false;
        }

        if (!ctype_digit($categoryId)) {
            $this->error('category_id must be an integer.');
            return false;
        }

        return true;
    }

    public function handle()
    {
        if (!$this->inputIsValid()) {
            return;
        }

        $categoryId = (int) $this->option('categoryId');
        $dateFrom = $this->option('dateFrom');
        $dateTo = $this->option('dateTo');

        $this->info("Split start");

        $connection = DB::connection(); // lock default connection

        $this->info("Split running ...");

        $this->initTempShareableTasks($connection, $categoryId, $dateFrom, $dateTo);
        $this->initTempTaskActivityMap($connection);
        $this->add($connection);
        // $this->prepareTempTables($connection, $categoryId, $dateFrom, $dateTo);

        $this->createTasks($connection);
        $this->updateTaskActivityMap($connection);
        $this->updateActivityRecords($connection);
        $this->createWorkorders($connection);

        $this->isValid($connection);
        $this->cleanUp($connection);

        $this->info("Split finished");

        return 0;
    }

    private function initTempShareableTasks(Connection $db, int $rootCategoryId, $dateFrom, $dateTo)
    {
        $this->info("init tmp_shareable_tasks");

        // filter relevant tasks that should be split
        $sql = "
            CREATE TEMPORARY TABLE tmp_shareable_tasks (
                task_id BIGINT UNSIGNED PRIMARY KEY
            )
        ";
        $db->statement($sql);

        $sql = "
            INSERT INTO tmp_shareable_tasks (task_id)        
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
            SELECT t.id
            FROM dpb_worktimefund_model_task t
            JOIN dpb_worktimefund_model_operation o 
                ON o.id = t.source_id
            JOIN category_tree ct
                ON o.parent_id = ct.id
            WHERE 
                o.is_shareable = 0
                AND t.is_shareable = 1
                AND t.created_at BETWEEN :dateFrom AND :dateTo
        ";

        $db->statement($sql, [
            'rootCategoryId' => $rootCategoryId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    private function initTempTaskActivityMap(Connection $db)
    {
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
                t.task_id
            FROM dpb_worktimefund_model_activityrecord ar
            JOIN tmp_shareable_tasks t
                ON t.task_id = ar.task_id
        ";
        $db->statement($sql);
    }

    /**
     * @TODO
     * @param Connection $db
     * @return void
     */
    private function add(Connection $db)
    {
        // add temporary migration UUID column
        $sql = "
            ALTER TABLE dpb_worktimefund_model_task
            ADD COLUMN migration_activity_id BIGINT UNSIGNED NULL       
        ";
        $db->statement($sql);
    }

    private function createTasks(Connection $db)
    {
        $this->info("create tasks");
        // CLONE tasks
        // every cloned task knows which activity it belongs to.
        $sql = "
            INSERT INTO dpb_worktimefund_model_task (
                source_id,
                title,
                expected_duration,
                is_shareable,
                status,
                department_id,
                created_at,
                updated_at,
                maintainable_type,
                maintainable_id,
                migration_activity_id
            )
            SELECT
                t.source_id,
                concat('XXXXXX_', t.title),
                t.expected_duration,
                0,
                t.status,
                t.department_id,
                t.created_at,
                t.updated_at,
                t.maintainable_type,
                t.maintainable_id,
                ar.id
            FROM dpb_worktimefund_model_activityrecord ar
            JOIN dpb_worktimefund_model_task t
                ON t.id = ar.task_id
            JOIN tmp_shareable_tasks st
                ON t.id = st.task_id
        ";

        $db->statement($sql);
    }

    private function createWorkorders(Connection $db)
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
    }

    private function isValid(Connection $db): bool
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

    private function cleanUp(Connection $db)
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

    private function updateActivityRecords(Connection $db)
    {
        $this->info("Update activity records");
        // -- make every activity point to its own dedicated task.
        // UPDATE dpb_worktimefund_model_activityrecord ar
        // JOIN tmp_task_activity_map m
        //     ON m.activity_id = ar.id
        // SET ar.task_id = m.new_task_id;
    }

    private function updateTaskActivityMap(Connection $db)
    {
        $this->info("Update task activity map");
        // -- populate new_task_id mapping
        // UPDATE tmp_task_activity_map m
        // JOIN dpb_worktimefund_model_task t
        //     ON t.migration_activity_id = m.activity_id
        // SET m.new_task_id = t.id;
    }
}
