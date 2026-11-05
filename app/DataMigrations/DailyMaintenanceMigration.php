<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\Modules\Tasks\Eligibility\Models\OperationRule;
use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Dpb\WorkTimeFund\Models\Operation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DailyMaintenanceMigration implements DataMigration
{
    private const SUBJECT_TYPE = 'vehicle';
    private const DAILY_MAINTENANCE_TIG_ID = 45;
    private const DAILY_MAINTENANCE_5MIN_ID = 290019360;
    private const WTF_OPERATION_CATEGORY_A = 5923;
    private const WTF_OPERATION_CATEGORY_ET = 5572;
    private const DEP_7213_ID = 334;
    private const DEP_7223_ID = 339;
    private const DEP_7233_ID = 343;
    private const DEP_5400_ID = 259;
    private const DEP_5621_ID = 277;
    private const DEP_5622_ID = 278;
    private const DEP_5521_ID = 269;
    private const DEP_5522_ID = 270;

    public function run(): void
    {
        $now = now();

        $taskGroupId = TaskGroup::where('code', 'daily-maintenance')
            ->first()->id;

        $taskItemGroups = TaskItemGroup::where('task_group_id', $taskGroupId)->get();

        $this->seedA(
            $taskGroupId,
            $taskItemGroups,
            $now
        );

        $this->seedT(
            $taskGroupId,
            $taskItemGroups,
            $now
        );

        $this->seedE(
            $taskGroupId,
            $taskItemGroups,
            $now
        );
    }

    private function seedA(
        int $taskGroupId,
        Collection $taskItemGroups,
        string $timestamp
    ) {
        $departmentIds = [
            self::DEP_7213_ID,
            self::DEP_7223_ID,
            self::DEP_7233_ID,
        ];

        $conditions = json_encode([
            'vehicle_type' => [
                'in' => ['A'],
            ],
        ]);

        $operationIds = Operation::where(
            'parent_id',
            self::WTF_OPERATION_CATEGORY_A
        )
            ->pluck('id')
            ->all();

        $this->seed(
            $taskGroupId,
            $departmentIds,
            $taskItemGroups,
            $conditions,
            $operationIds,
            $timestamp
        );
    }

    private function seedT(
        int $taskGroupId,
        Collection $taskItemGroups,
        string $timestamp
    ) {
        $departmentIds = [
            self::DEP_5521_ID,
            self::DEP_5522_ID,
        ];

        $conditions = json_encode([
            'vehicle_type' => [
                'in' => ['T'],
            ],
        ]);

        $operationIds = Operation::where(
            'parent_id',
            self::WTF_OPERATION_CATEGORY_ET
        )
            ->pluck('id')
            ->all();

        $this->seed(
            $taskGroupId,
            $departmentIds,
            $taskItemGroups,
            $conditions,
            $operationIds,
            $timestamp
        );
    }

    private function seedE(
        int $taskGroupId,
        Collection $taskItemGroups,
        string $timestamp
    ) {
        $departmentIds = [
            self::DEP_5621_ID,
            self::DEP_5622_ID,
            self::DEP_5400_ID,
        ];

        $conditions = json_encode([
            'vehicle_type' => [
                'in' => ['E'],
            ],
        ]);

        $operationIds = Operation::where(
            'parent_id',
            self::WTF_OPERATION_CATEGORY_ET
        )
            ->pluck('id')
            ->all();

        $this->seed(
            $taskGroupId,
            $departmentIds,
            $taskItemGroups,
            $conditions,
            $operationIds,
            $timestamp
        );
    }

    public function run1(): void
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

    private function seed(
        int $taskGroupId,
        array $departmentIds,
        Collection $taskItemGroups,
        string $conditions,
        array $operationIds,
        string $timestamp
    ) {
        $rules = [];

        foreach ($departmentIds as $departmentId) {

            $taskBatchDepartmentGroupId = DB::table('tms_task_batch_department_group')->insertGetId(
                [
                    'department_id' => $departmentId,
                    'task_group_id' => $taskGroupId,
                    'mode' => TaskBatchMode::Uniform->value,
                ]
            );

            foreach ($taskItemGroups as $tig) {
                DB::table('tms_task_batch_department_group_items')->upsert(
                    [
                        'tbdg_id' => $taskBatchDepartmentGroupId,
                        'task_item_group_id' => $tig->id,
                        // 'label' => isset($tig['label']) ? $tig['label'] : $tig['title'],
                        'label' => $tig->title,
                        'is_scalable' => false,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ],
                    ['tbdg_id', 'task_item_group_id']
                );

                foreach ($operationIds as $operationId) {
                    $rules[] = [
                        'task_item_group_id' => $tig->id,
                        'subject_type' => self::SUBJECT_TYPE,
                        'operation_id' => $operationId,
                        'conditions' => $conditions,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }
        }

        OperationRule::insert($rules);
    }
}
