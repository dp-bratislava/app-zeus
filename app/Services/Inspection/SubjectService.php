<?php

namespace App\Services\Inspection;

use App\Models\InspectionSubject;
use Dpb\Package\Inspections\Models\Inspection;
use Illuminate\Database\Eloquent\Model;

class SubjectService
{
    public function __construct(protected InspectionSubject $inspectionSubject) {}

    public function getSubject(Inspection $inspection): Model|null
    {        
        return $this->inspectionSubject
            ->with('subject')
            ->where('inspection_id', '=', $inspection->id)
            ->first()
            ?->subject;
    }
}
