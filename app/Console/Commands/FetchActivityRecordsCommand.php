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
        $records = DB::select('
            SELECT 
            	id,
                title,
                source_id,
                expected_duration,
                real_duration,
                personal_id,
                task_id,
                id_podzakazky,
                date
            FROM (
                SELECT *,
                       -- Total rows in the group
                       COUNT(*) OVER(PARTITION BY id_podzakazky, source_id) AS total_count,
                       -- Total distinct task_ids in the group
                       MAX(task_rank) OVER(PARTITION BY id_podzakazky, source_id) AS distinct_task_count
                FROM (
                    SELECT *,
                           DENSE_RANK() OVER(PARTITION BY id_podzakazky, source_id ORDER BY task_id) AS task_rank
                    FROM (
                        SELECT 
                            ar.id,
                            ar.title,
                            wt.source_id,
                            ar.expected_duration,
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
        $groupedResults = collect($records)->groupBy(['id_podzakazky', 'source_id']);

        foreach ($groupedResults as $id_podzakazky => $sourceGroups) {
            $this->info('ID Podzákazky: ' . $id_podzakazky);

            foreach ($sourceGroups as $source_id => $recordsList) {
                $pocet_ludi = count($recordsList->pluck('personal_id')->unique());
                $pocet_zaznamov = count($recordsList);
                $this->info('  Source ID: ' . $source_id . ', Rôzny ľudia: ' . $pocet_ludi);
                
                // 1. Get unique task IDs first
                $uniqueTasks = $recordsList->pluck('task_id')->unique();

                // 2. Shift the first ID out of the collection
                $worktask_to_modify = $uniqueTasks->shift();

                // 3. The rest are left in the collection; convert to a plain array
                $rest_of_worktasks = $uniqueTasks->values()->all();

                if($pocet_ludi != $pocet_zaznamov) {
                    $this->error('    POZOR: Počet rôznych ľudí sa nezhoduje s počtom záznamov!');
                }
                else {
                    
                }
    
                // foreach ($recordsList as $record) {
                //     $this->info('    Record ID: ' . $record->id . ', Title: ' . $record->title);
                // }

            }
        }
        return 0;
    }
}