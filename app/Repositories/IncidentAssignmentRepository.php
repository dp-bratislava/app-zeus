<?php

namespace App\Repositories;

use App\Data\Incident\IncidentAssignmentData;
use App\Mappers\Incident\IncidentAssignmentMapper;
use App\Mappers\Incident\IncidentMapper;
use App\Models\IncidentAssignment;

class IncidentAssignmentRepository
{
    public function __construct(
        private IncidentAssignment $eloquentModel,
        private IncidentMapper $incidentMapper,
        private IncidentAssignmentMapper $incidentAssignmentMapper
        ) {}

    public function findById(int $id): ?IncidentAssignment
    {
        $model = $this->eloquentModel->findOrFail($id);

        return $model;
    }

    public function save(IncidentAssignmentData $incidentAssignmentData): ?IncidentAssignment
    {
        // create incident
        $incident = $this->incidentMapper->toEloquent($incidentAssignmentData->incident);
        $incident->save();

        $incidentAssignment = $this->incidentAssignmentMapper->toEloquent($incidentAssignmentData);
        // dd($incidentAssignment);
        $incidentAssignment->incident()->associate($incident);
        $incidentAssignment->save();

        return $incidentAssignment;
    }

    public function push(IncidentAssignment $incidentAssignment): ?IncidentAssignment
    {
        $incidentAssignment->push();
        return $incidentAssignment;
    }    
}