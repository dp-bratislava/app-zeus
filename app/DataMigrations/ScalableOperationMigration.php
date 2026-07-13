<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\DatahubSync\Models\Department;
use Dpb\Package\TaskMS\Models\DepartmentAssignment;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Dpb\WorkTimeFund\Models\Category;
use Dpb\WorkTimeFund\Models\Operation;
use Illuminate\Support\Facades\DB;

/**
 * Set all operations as scalable for department 9486
 */
class ScalableOperationMigration implements DataMigration
{
    public function run(): void
    {
        // assigna all available taskitemgroups to
        // specified departments (maintenance group departments)
        $departmentIds = Department::query()
            ->whereIn('code', [
                '9486',
            ])
            ->pluck('id');

        Operation::whereIn('parent_id', function ($query) use ($departmentIds) {
            $query->select('morphable_id')
                ->from('dpb_departments_mm_morphable_department')
                ->where('morphable_type', Category::class)
                ->whereIn('department_id', $departmentIds);
        })->update([
            'is_scalable' => true,
        ]);
    }
}
