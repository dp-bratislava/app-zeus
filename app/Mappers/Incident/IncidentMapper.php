<?php

namespace App\Mappers\Incident;

use App\Data\Incident\IncidentData;
use Dpb\Package\Incidents\Models\Incident;

// use Illuminate\Database\Eloquent\Collection;

class IncidentMapper
{

    public function __construct(
        private Incident $eloquentModel
    ) {}

    public function toEloquent(IncidentData $incidentData): Incident
    {
        $model = $this->eloquentModel->firstOrNew(['id' => $incidentData->id]);
        $model->fill($incidentData->toArray());

        return $model;
    }
}
