<?php

namespace App\Mappers\Inspection;

use App\Data\Inspection\InspectionAssignmentData;
use App\Models\InspectionAssignment;

// use Illuminate\Database\Eloquent\Collection;

class InspectionAssignmentMapper
{

    public function __construct(
        private InspectionAssignment $eloquentModel
    ) {}

    public function toEloquent(InspectionAssignmentData $inspectionAssignmentData): InspectionAssignment
    {
        $model = $this->eloquentModel->firstOrNew(['id' => $inspectionAssignmentData->id]);

        // dd($inspectionAssignmentData->toArray());
        $model->fill($inspectionAssignmentData->toArray());

        return $model;
    }
}
