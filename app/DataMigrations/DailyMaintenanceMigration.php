<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\Modules\Tasks\Eligibility\Models\OperationRule;
use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Dpb\WorkTimeFund\Models\Operation;
use Illuminate\Support\Facades\DB;

class DailyMaintenanceMigration implements DataMigration
{
    private const SUBJECT_TYPE = 'vehicle';
    private const DAILY_MAINTENANCE_TIG_ID = 45;
    private const DAILY_MAINTENANCE_5MIN_ID = 290019360;
    private const DEP_7213_ID = 334;
    private const DEP_7223_ID = 339;
    private const DEP_7233_ID = 343;

    public function run(): void
    {
        $now = now();

        $taskGroupId = TaskGroup::where('code', 'daily-maintenance')
            ->first()->id;

        // task batch group department and mode binding
        $departments = [
            self::DEP_7213_ID,
            self::DEP_7223_ID,
            self::DEP_7233_ID,
        ];

        $tig = TaskItemGroup::whereKey(self::DAILY_MAINTENANCE_TIG_ID)->first();

        foreach ($departments as $departmentId) {

            $taskBatchDepartmentGroupId = DB::table('tms_task_batch_department_group')->insertGetId(
                [
                    'department_id' => $departmentId,
                    'task_group_id' => $taskGroupId,
                    'mode' => TaskBatchMode::Uniform->value,
                ]
            );

            DB::table('tms_task_batch_department_group_items')->upsert(
                [
                    'tbdg_id' => $taskBatchDepartmentGroupId,
                    'task_item_group_id' => $tig->id,
                    // 'label' => isset($tig['label']) ? $tig['label'] : $tig['title'],
                    'label' => $tig->title,
                    'is_scalable' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                ['tbdg_id', 'task_item_group_id']
            );
        }

        $rules = [];
        // bind operation categories to subject conditions
        $conditions = json_encode([
            'vehicle_type' => [
                'in' => ['A'],
            ],
        ]);
        $rules[] = [
            'task_item_group_id' => TaskItemGroup::whereKey(self::DAILY_MAINTENANCE_TIG_ID)
                ->first()->id,
            'subject_type' => self::SUBJECT_TYPE,
            'operation_id' => Operation::whereKey(self::DAILY_MAINTENANCE_5MIN_ID)
                ->first()->id,
            'conditions' => $conditions,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        OperationRule::insert($rules);
    }
}
