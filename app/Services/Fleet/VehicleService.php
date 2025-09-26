<?php

namespace App\Services\Fleet;

use App\Models\Datahub\Department;
use App\Models\DepartmentAssignment;
use Dpb\DatahubSync\Models\Department as ModelsDepartment;
use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;

class VehicleService
{
    public function __construct(protected DepartmentAssignment $departmentAssignment) {}

    // public function assignUnitRate(ActivityTemplate $tempalte, UnitRate $unitRate)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    public function getDepartment(Vehicle $vehicle): Model|null
    {
        return $this->departmentAssignment
            ->with('department')
            ->where('subject_id', '=', $vehicle->id)
            ->where('subject_type', '=', 'vehicle')
            ->first()
            ?->department;
    }

    public function setDepartment(Vehicle $vehicle, Department|int $department)
    {
        $this->departmentAssignment->createOrFirst(
            [
                'department_id' => ($department instanceof (Department::class)) ? $department->id : $department,
                'subject_id' => $vehicle->id,
                'subject_type' => 'vehicle'
            ],
        );
    }
}
