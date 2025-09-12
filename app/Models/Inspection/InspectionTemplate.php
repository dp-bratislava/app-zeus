<?php

namespace App\Models\Inspection;

use App\Models\Fleet\VehicleModel;
use Dpb\Packages\Inspections\Models\InspectionTemplate as BaseInspectionTempalte;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class InspectionTemplate extends BaseInspectionTempalte
{
    public function vehicleModels(): MorphToMany
    {
        return $this->morphedByMany(
            VehicleModel::class, 
            'subject',       
            config('pkg-inspections.table_prefix') . 'inspection_templatables',    
            'template_id',
            'subject_id',
        )
        ->withTimestamps()
        ;
    }
}
