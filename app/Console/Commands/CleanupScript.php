<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupScript extends Command
{
    protected $signature = 'cleanup:script';

    protected $description = 'Perform cleanup operations on the database';

    public function handle()
    {
        DB::affectingStatement('DROP TABLE IF EXISTS TEMP_activity_record_updates');
        DB::statement('CREATE TABLE TEMP_activity_record_updates (id BIGINT UNSIGNED, old_task_id BIGINT UNSIGNED, new_task_id BIGINT UNSIGNED, old_expected_duration INT UNSIGNED, new_expected_duration INT UNSIGNED)');
        DB::affectingStatement('DROP TABLE IF EXISTS TEMP_work_task_updates');
        DB::statement('CREATE TABLE TEMP_work_task_updates (id BIGINT UNSIGNED, is_shareable_old TINYINT(1), is_shareable_new TINYINT(1))');        

        $records = DB::select('
            SELECT 
            	id,
                title,
                type,
                source_id,
                is_shareable,
                operation_duration,
                expected_duration,
                real_duration,
                personal_id,
                task_id,
                id_podzakazky,
                date
            FROM (
                SELECT *,
                       -- Total rows in the group
                       COUNT(*) OVER(PARTITION BY id_podzakazky, source_id, date) AS total_count,
                       -- Total distinct task_ids in the group
                       MAX(task_rank) OVER(PARTITION BY id_podzakazky, source_id, date) AS distinct_task_count
                FROM (
                    SELECT *,
                           DENSE_RANK() OVER(PARTITION BY id_podzakazky, source_id, date ORDER BY task_id) AS task_rank
                    FROM (
                        SELECT 
                            ar.id,
                            ar.title,
                            ar.type,
                            wt.source_id,
                            wt.is_shareable,
                            ar.expected_duration,
                            operation.duration as operation_duration,
                            ar.real_duration,
                            ar.personal_id,
                            ar.task_id,
                            wo.tms_task_item_id AS id_podzakazky,
                            ar.date
                        FROM dpb_worktimefund_model_activityrecord ar
                        JOIN dpb_wtftmsbridge_mm_workorder_task wot ON ar.task_id = wot.taskitem_id 
                        JOIN dpb_wtftmsbridge_model_workorder AS wo ON wot.workorder_id = wo.id 
                        JOIN dpb_worktimefund_model_task wt ON ar.task_id = wt.id 
                        JOIN dpb_worktimefund_model_operation operation ON wt.source_id = operation.id 
                        WHERE operation.is_shareable = 1
                          AND ar.`date` < \'2026-05-15\'
                          AND ar.`department_id` in (334, 339, 343)
                          AND ar.real_duration < ar.expected_duration
                    ) inner_base
                ) ranked_base
            ) sub
            WHERE total_count > 1 
              AND total_count = distinct_task_count
        ');
        $groupedResults = collect($records)->groupBy(['id_podzakazky', 'source_id', 'date']);
        $activityRecordUpdates = [];
        $workTasksUpdates = [];
        $workTasksToRemove = [];
        foreach ($groupedResults as $id_podzakazky => $sourceGroups) {
            foreach ($sourceGroups as $source_id => $dateGroups) {
                foreach ($dateGroups as $date => $recordsList) {

                    if ($recordsList->isEmpty()) {
                        $this->error('FATAL ERROR NIECO JE FAKT ZLE');    
                        continue;
                    }

                    if($this->employeeCountMismatch($recordsList)) {
                        $this->error('    POZOR: Iny pocet ludi a zaznamov! . id podzakazky: ' . $id_podzakazky . ' source_id: ' . $source_id . ' date: ' . $date);
                        continue;
                    }

                    $duration = $recordsList->first()->operation_duration;
                    if($duration === 0) {
                        $this->error('    POZOR: operation_duration je null! . id podzakazky: ' . $id_podzakazky . ' source_id: ' . $source_id . ' date: ' . $date);
                        continue;
                    }
                    $duration_per_task = $duration / count($recordsList);
                    $uniqueTasks = $recordsList->pluck('task_id')->unique();
                    $worktask_to_modify = $uniqueTasks->shift();
                    $rest_of_worktasks = $uniqueTasks->values()->all();
                    
                    foreach ($recordsList as $record) {
                        if($record->type === 'A'){$this->error('FATAL ERROR JE TU DOVOLENKA');}
                        $activityRecordUpdates[] = [
                            'id' => $record->id,
                            'old_task_id' => $record->task_id,
                            'new_task_id' => $worktask_to_modify,
                            'old_expected_duration' => $record->expected_duration,
                            'new_expected_duration' => $duration_per_task,
                        ];
                        // should execute only once per date group
                        if($record->task_id == $worktask_to_modify) {
                            $workTasksUpdates[] = [
                            'id' => $worktask_to_modify,
                            'is_shareable_old' => $record->is_shareable,
                            'is_shareable_new' => 1,
                        ];
                        }
                    }
                    
                    $workTasksToRemove = array_merge($workTasksToRemove, $rest_of_worktasks);
                    

                }
            }
        }


        // 3. Insert the updates into the temporary table
        if (!empty($activityRecordUpdates)) {
        DB::table('TEMP_activity_record_updates')->insert($activityRecordUpdates);
        }

        if (!empty($workTasksUpdates)) {
        DB::table('TEMP_work_task_updates')->insert($workTasksUpdates);
        }

        if (!empty($workTasksToRemove)) {
            // after deleting the tasks, cascade will remove records in TEMP_mm_workorder_task_removed so we back the values up first
            DB::statement('DROP TABLE IF EXISTS TEMP_mm_workorder_task_removed');
            DB::statement('CREATE TABLE TEMP_mm_workorder_task_removed AS SELECT workorder_id, taskitem_id FROM dpb_wtftmsbridge_mm_workorder_task WHERE taskitem_id IN (' . implode(',', $workTasksToRemove) . ')');
            
            DB::statement('DROP TABLE IF EXISTS TEMP_work_tasks_removed');
            DB::statement('CREATE TABLE TEMP_work_tasks_removed AS SELECT * FROM dpb_worktimefund_model_task WHERE id IN (' . implode(',', $workTasksToRemove) . ')');
            DB::statement('DELETE FROM dpb_worktimefund_model_task WHERE id IN (' . implode(',', $workTasksToRemove) . ')');

        }

        if (!empty($workTasksUpdates)) {
            DB::statement('
            UPDATE dpb_worktimefund_model_task
            JOIN TEMP_work_task_updates ON dpb_worktimefund_model_task.id = TEMP_work_task_updates.id 
            SET dpb_worktimefund_model_task.is_shareable = TEMP_work_task_updates.is_shareable_new
            ');
        }

        if (!empty($activityRecordUpdates)) {
            DB::statement('
            UPDATE dpb_worktimefund_model_activityrecord
            JOIN TEMP_activity_record_updates ON dpb_worktimefund_model_activityrecord.id = TEMP_activity_record_updates.id 
            SET dpb_worktimefund_model_activityrecord.task_id = TEMP_activity_record_updates.new_task_id,
                dpb_worktimefund_model_activityrecord.expected_duration = TEMP_activity_record_updates.new_expected_duration
            ');
        }



        return 0;
    }

    private function employeeCountMismatch($recordsList)
    {
        $pocet_ludi = count($recordsList->pluck('personal_id')->unique());
        $pocet_zaznamov = count($recordsList);
        return $pocet_ludi != $pocet_zaznamov;
    }
}