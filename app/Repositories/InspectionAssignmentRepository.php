<?php

namespace App\Repositories;

use App\Data\Inspection\InspectionAssignmentData;
use App\Mappers\Inspection\InspectionAssignmentMapper;
use App\Mappers\Inspection\InspectionMapper;
use App\Models\InspectionAssignment;

class InspectionAssignmentRepository
{
    public function __construct(
        private InspectionAssignment $eloquentModel,
        private InspectionMapper $inspectionMapper,
        private InspectionAssignmentMapper $inspectionAssignmentMapper
        ) {}

    public function findById(int $id): ?InspectionAssignment
    {
        $model = $this->eloquentModel->findOrFail($id);

        return $model;
    }

    public function save(InspectionAssignmentData $inspectionAssignmentData): ?InspectionAssignment
    {
        // create inspection
        $inspection = $this->inspectionMapper->toEloquent($inspectionAssignmentData->inspection);
        $inspection->save();

        $inspectionAssignment = $this->inspectionAssignmentMapper->toEloquent($inspectionAssignmentData);
        // dd($inspectionAssignment);
        $inspectionAssignment->inspection()->associate($inspection);
        $inspectionAssignment->save();

        return $inspectionAssignment;
    }

    public function push(InspectionAssignment $inspectionAssignment): ?InspectionAssignment
    {
        $inspectionAssignment->push();
        return $inspectionAssignment;
    }    
}
