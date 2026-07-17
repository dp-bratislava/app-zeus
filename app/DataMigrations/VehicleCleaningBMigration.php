<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\DatahubSync\Models\Department;
use Dpb\Package\TaskMS\Models\DepartmentAssignment;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;

/**
 * TaskGroups and TaskItemGroups data bound to specific departments
 * for vehicle cleaning department addition to system
 */
class VehicleCleaningBMigration implements DataMigration
{
    public function run(): void
    {
        // add vehicle cleaning b task group
        $group = TaskGroup::firstOrCreate(
            ['code' => 'vehicle-cleaning-b'],
            [
                'title' => 'Čistenie B',
            ]
        );

        // add vehicle cleaning b task item group
        $itemGroup = TaskItemGroup::firstOrCreate(
            ['code' => 'vehicle-cleaning-b'],
            [
                'title' => 'Čistenie B',
                'task_group_id' => $group->id,
                'automatic_create' => true,
            ]
        );

        // assign cleaning group and item group to 
        // cleaning department
        $cleaningDepartmentId = Department::where('code', '=', '9486')->first()->id;
        DepartmentAssignment::firstOrCreate([
            'department_id' => $cleaningDepartmentId,
            'subject_id' => $group->id,
            'subject_type' => 'task-group',
        ]);
        DepartmentAssignment::firstOrCreate([
            'department_id' => $cleaningDepartmentId,
            'subject_id' => $itemGroup->id,
            'subject_type' => 'task-item-group',
        ]);

        // assigna all available taskitemgroups to
        // specified departments (maintenance group departments)
        $departmentIds = Department::query()
            ->whereIn('code', [
                '5400',
                '5621',
                '5622',
                '5521',
                '5522',
                '7213',
                '7223',
                '7233'
            ])
            ->pluck('id');

        $itemGroupIds = TaskItemGroup::pluck('id');

        foreach ($departmentIds as $departmentId) {
            DepartmentAssignment::firstOrCreate([
                'department_id' => $departmentId,
                'subject_id' => $group->id,
                'subject_type' => 'task-group',
            ]);

            foreach ($itemGroupIds as $itemGroupId) {

                DepartmentAssignment::firstOrCreate([
                    'department_id' => $departmentId,
                    'subject_id' => $itemGroupId,
                    'subject_type' => 'task-item-group',
                ]);
            }
        }
    }
}
