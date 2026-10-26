<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\DatahubSync\Models\Department;
use Dpb\Modules\Tasks\Eligibility\Models\OperationRule;
use Dpb\Package\TaskMS\Models\DepartmentAssignment;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Dpb\WorkTimeFund\Models\Category;
use Dpb\WorkTimeFund\Models\Operation;
use Illuminate\Support\Facades\DB;

class DailyMaintenanceMigration implements DataMigration
{
    private const SUBJECT_TYPE = 'vehicle';

    public function run(): void
    {
        $now = now();

        $tig = TaskItemGroup::whereKey(45)->first();

        DB::table('tms_task_batch_department_tigs')->upsert(
            [
                [
                    'department_id' => 334,
                    'task_item_group_id' => $tig->id,
                    // 'label' => isset($tig['label']) ? $tig['label'] : $tig['title'],
                    'label' => $tig->title,
                    'is_scalable' => 0,
                ],
                [
                    'department_id' => 339,
                    'task_item_group_id' => $tig->id,
                    // 'label' => isset($tig['label']) ? $tig['label'] : $tig['title'],
                    'label' => $tig->title,
                    'is_scalable' => 0,
                ],
                [
                    'department_id' => 343,
                    'task_item_group_id' => $tig->id,
                    // 'label' => isset($tig['label']) ? $tig['label'] : $tig['title'],
                    'label' => $tig->title,
                    'is_scalable' => 0,
                ]
            ],
            ['department_id', 'task_item_group_id']
        );

        $rules = [];
        // bind operation categories to subject conditions
        $conditions = json_encode([
            'vehicle_type' => [
                'in' => ['A'],
            ],
        ]);
        $rules[] = [
            'task_item_group_id' => TaskItemGroup::whereKey(45)
                ->first()->id,
            'subject_type' => self::SUBJECT_TYPE,
            'operation_id' => Operation::whereKey(290019360)
                ->first()->id,
            'conditions' => $conditions,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        OperationRule::insert($rules);
    }
}
