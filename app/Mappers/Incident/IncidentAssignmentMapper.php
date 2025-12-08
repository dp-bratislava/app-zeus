<?php

namespace App\Mappers\Incident;

use App\Data\Incident\IncidentAssignmentData;
use App\Models\IncidentAssignment;

// use Illuminate\Database\Eloquent\Collection;

class IncidentAssignmentMapper
{

    public function __construct(
        private IncidentAssignment $eloquentModel
    ) {}

    public function toEloquent(IncidentAssignmentData $incidentAssignmentData): IncidentAssignment
    {
        $model = $this->eloquentModel->firstOrNew(['id' => $incidentAssignmentData->id]);

        // dd($incidentAssignmentData->toArray());
        $model->fill($incidentAssignmentData->toArray());

        return $model;
    }
}
