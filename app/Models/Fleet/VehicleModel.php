<?php

namespace App\Models\Fleet;

use App\Models\Inspection\InspectionTemplate;
use Dpb\Packages\Vehicles\Models\VehicleModel as BaseVehicleModel;

class VehicleModel extends BaseVehicleModel
{
    public function inspectionTempaltes()
    {
        return $this->morphToMany(
            InspectionTemplate::class,
            'subject',
            config('pkg-inspections.table_prefix') . 'inspection_templatables',    
            'subject_type',
            'subject_id',
        )
        ->withTimestamps();
    }
}
