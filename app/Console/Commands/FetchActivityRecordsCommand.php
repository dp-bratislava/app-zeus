<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchActivityRecordsCommand extends Command
{
    protected $signature = 'fetch:activity-records';

    protected $description = 'Fetch activity records based on complex query criteria';

    public function handle()
    {
        DB::affectingStatement('DROP TABLE IF EXISTS activity_record_updates');
        DB::statement('CREATE TABLE activity_record_updates (id BIGINT UNSIGNED, new_task_id BIGINT UNSIGNED)');
        DB::affectingStatement('DROP TABLE IF EXISTS workt_task_updates');
        DB::statement('CREATE TABLE workt_task_updates (id BIGINT UNSIGNED, new_task_id BIGINT UNSIGNED)');        

        $records = DB::select('
            SELECT 
            	id,
                title,
                source_id,
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
                            wt.source_id,
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
        // 1. Add 'date' to the groupBy array
        $groupedResults = collect($records)->groupBy(['id_podzakazky', 'source_id', 'date']);
        $activityRecordUpdates = [];
        $workTasksUpdates = [];
        $workTasksToRemove = [];
        foreach ($groupedResults as $id_podzakazky => $sourceGroups) {
            foreach ($sourceGroups as $source_id => $dateGroups) {
                // 2. Add the third loop layer to iterate over each day
                foreach ($dateGroups as $date => $recordsList) {
                    
                    $pocet_ludi = count($recordsList->pluck('personal_id')->unique());
                    $pocet_zaznamov = count($recordsList);
                    
                    $uniqueTasks = $recordsList->pluck('task_id')->unique();
                    $worktask_to_modify = $uniqueTasks->shift();
                    $rest_of_worktasks = $uniqueTasks->values()->all();
                    $workTasksToRemove = array_merge($workTasksToRemove, $rest_of_worktasks);
                    $ids = $recordsList->pluck('id')->all();
                    


                    if($pocet_ludi != $pocet_zaznamov) {
                        $this->error('    POZOR: Iny pocet ludi a zaznamov! . id podzakazky: ' . $id_podzakazky . ' source_id: ' . $source_id . ' date: ' . $date);
                        continue;
                    }
                    foreach ($ids as $id) {
                        $activityRecordUpdates[] = [
                            'id' => $id, 
                            'new_task_id' => $worktask_to_modify
                        ];
                    }
                }
            }
        }
        // 3. Insert the updates into the temporary table
        DB::table('activity_record_updates')->insert($activityRecordUpdates);

        // DB::statement('
        // UPDATE products 
        // JOIN activity_record_updates ON products.id = activity_record_updates.id 
        // SET products.price = activity_record_updates.new_price
        // ');

        // DB::statement('DROP TEMPORARY TABLE temp_updates');



        return 0;
    }
}