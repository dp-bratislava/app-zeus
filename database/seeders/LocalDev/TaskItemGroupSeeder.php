<?php

namespace Database\Seeders\LocalDev;

use Dpb\Departments\Models\Department;
use Dpb\Package\Fleet\Models\VehicleModel;
use Dpb\Package\TaskMS\Models\DepartmentAssignment;
use Dpb\Package\TaskMS\Models\TaskItemGroupAssignment;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Illuminate\Database\Seeder;

class TaskItemGroupSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::query()->first();

        if (! $department) {
            $this->command?->warn('No department found — skipping task item group seed.');

            return;
        }

        $taskItemGroupsByTaskGroup = [
            'oprava' => [
                ['code' => 'malfunction', 'title' => 'Porucha'],
                ['code' => 'repair', 'title' => 'Oprava'],
                ['code' => 'bodywork', 'title' => 'Karoséria'],
            ],
            'daily-maintenance' => [
                ['code' => 'vehicle-cleaning-b', 'title' => 'Umývanie vozidla'],
                ['code' => 'daily-check', 'title' => 'Denná kontrola'],
            ],
            'inspection' => [
                ['code' => 'technical-inspection', 'title' => 'Technická kontrola'],
            ],
        ];

        $taskItemGroups = collect();

        foreach ($taskItemGroupsByTaskGroup as $taskGroupCode => $groups) {
            $taskGroup = TaskGroup::byCode($taskGroupCode)->first();

            if (! $taskGroup) {
                continue;
            }

            foreach ($groups as $group) {
                $taskItemGroups->push(TaskItemGroup::firstOrCreate(
                    ['code' => $group['code']],
                    [
                        'title' => $group['title'],
                        'task_group_id' => $taskGroup->id,
                    ],
                ));
            }
        }

        foreach ($taskItemGroups as $taskItemGroup) {
            DepartmentAssignment::firstOrCreate([
                'department_id' => $department->id,
                'subject_id' => $taskItemGroup->id,
                'subject_type' => app(TaskItemGroup::class)->getMorphClass(),
            ]);

            foreach (VehicleModel::pluck('id') as $vehicleModelId) {
                TaskItemGroupAssignment::firstOrCreate([
                    'group_id' => $taskItemGroup->id,
                    'subject_id' => $vehicleModelId,
                    'subject_type' => app(VehicleModel::class)->getMorphClass(),
                ]);
            }
        }
    }
}
