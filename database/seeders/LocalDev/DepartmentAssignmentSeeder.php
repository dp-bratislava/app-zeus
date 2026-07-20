<?php

namespace Database\Seeders\LocalDev;

use Dpb\Departments\Models\Department;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\VehicleType;
use Dpb\Package\TaskMS\Models\DepartmentAssignment;
use Dpb\Package\TaskMS\Models\DepartmentMaintenanceGroup;
use Dpb\Package\Tasks\Models\TaskGroup;
use Illuminate\Database\Seeder;

class DepartmentAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::query()->first();

        if (! $department) {
            $this->command?->warn('No department found — skipping department assignment seed.');

            return;
        }

        $this->call(TaskReferenceSeeder::class);
        $this->call(TaskItemGroupSeeder::class);

        $taskGroup = TaskGroup::byCode('oprava')->first();

        $subjects = [
            ...VehicleType::pluck('id')->map(fn (int $id) => [
                'subject_id' => $id,
                'subject_type' => app(VehicleType::class)->getMorphClass(),
            ]),
            ...MaintenanceGroup::pluck('id')->map(fn (int $id) => [
                'subject_id' => $id,
                'subject_type' => app(MaintenanceGroup::class)->getMorphClass(),
            ]),
            [
                'subject_id' => $taskGroup->id,
                'subject_type' => app(TaskGroup::class)->getMorphClass(),
            ],
        ];

        foreach ($subjects as $subject) {
            DepartmentAssignment::firstOrCreate([
                'department_id' => $department->id,
                'subject_id' => $subject['subject_id'],
                'subject_type' => $subject['subject_type'],
            ]);
        }

        foreach (MaintenanceGroup::pluck('id') as $maintenanceGroupId) {
            DepartmentMaintenanceGroup::firstOrCreate([
                'department_id' => $department->id,
                'maintenance_group_id' => $maintenanceGroupId,
            ]);
        }
    }
}
