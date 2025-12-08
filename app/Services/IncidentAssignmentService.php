<?php

namespace App\Services;

use App\Data\Incident\IncidentAssignmentData;
use App\Models\IncidentAssignment;
use App\Repositories\IncidentAssignmentRepository;

class IncidentAssignmentService
{
    public function __construct(
        protected IncidentAssignmentRepository $incidentAssignmentRepository,
    ) {}

    public function create(IncidentAssignmentData $incidentAssignmentData): ?IncidentAssignment
    {
        return $this->incidentAssignmentRepository->save($incidentAssignmentData);
    }

    public function update(IncidentAssignmentData $incidentAssignmentData): ?IncidentAssignment
    {
        return $this->incidentAssignmentRepository->save($incidentAssignmentData);
    }
}
