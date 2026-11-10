<?php

namespace App\DataMigrations\TaskBatch;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\Package\TaskMS\Models\DepartmentAssignment;
use Dpb\Package\Tasks\Models\Task;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;

class ReconcileOldDataMigration implements DataMigration
{
    public function run(): void
    {
        $cleaningBTg = TaskGroup::where('code', 'vehicle-cleaning-b')->first();
        $cleaningFloorTg = TaskGroup::where('code', 'vehicle-cleaning-floor')->first();
        $vehicleCleaningTg = TaskGroup::where('code', 'vehicle-cleaning')->first();

        $cleaningBTigIds = TaskItemGroup::where('task_group_id', $cleaningBTg->id)
            ->pluck('id');
        $cleaningFloorTigIds = TaskItemGroup::where('task_group_id', $cleaningFloorTg->id)
            ->pluck('id');
        $vehicleCleaningTigIds = TaskItemGroup::where('task_group_id', $vehicleCleaningTg->id)
            ->pluck('id');

        // reassign tasks to new task group
        Task::where('group_id', $cleaningBTg->id)
            ->update(
                [
                    'group_id' => $vehicleCleaningTg->id
                ],
            );

        // reassign task groups to departments
        DepartmentAssignment::where('subject_type', 'task-group')
            ->where('subject_id', $cleaningFloorTg->id)
            ->delete();

        DepartmentAssignment::where('subject_type', 'task-group')
            ->where('subject_id',    $cleaningBTg->id)
            ->update([
                'subject_id' => $vehicleCleaningTg->id
            ]);

        DepartmentAssignment::where('subject_type', 'task-item-group')
            ->whereIn(
                'subject_id',
                $cleaningBTigIds->merge($cleaningFloorTigIds)
            )
            ->delete();

        // delete old task groups
        TaskGroup::whereKey($cleaningBTg->id)->delete();
        TaskGroup::whereKey($cleaningFloorTg->id)->delete();
    }
}
