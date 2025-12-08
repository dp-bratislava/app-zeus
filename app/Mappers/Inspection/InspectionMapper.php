<?php

namespace App\Mappers\Inspection;

use App\Data\Inspection\InspectionData;
use Dpb\Package\Inspections\Models\Inspection;

class InspectionMapper
{

    public function __construct(
        private Inspection $eloquentModel
    ) {}

    public function toEloquent(InspectionData $inspectionData): Inspection
    {
        $model = $this->eloquentModel->firstOrNew(['id' => $inspectionData->id]);
        $model->fill($inspectionData->toArray());

        return $model;
    }
}
