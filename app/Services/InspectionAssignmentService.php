<?php

namespace App\Services;

use App\Data\Inspection\InspectionAssignmentData;
use App\Models\InspectionAssignment;
use App\Repositories\InspectionAssignmentRepository;
use Illuminate\Database\ConnectionInterface;

class InspectionAssignmentService
{
    public function __construct(
        protected InspectionAssignmentRepository $inspectionAssignmentRepository,
    ) {}

    public function create(InspectionAssignmentData $inspectionAssignmentData): ?InspectionAssignment
    {
        return $this->inspectionAssignmentRepository->save($inspectionAssignmentData);
    }

    public function update(InspectionAssignmentData $inspectionAssignmentData): ?InspectionAssignment
    {
        return $this->inspectionAssignmentRepository->save($inspectionAssignmentData);
    } 
}
