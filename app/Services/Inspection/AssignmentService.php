<?php

namespace App\Services\Inspection;

use App\Models\InspectionAssignment;
use Dpb\Package\Inspections\Models\Inspection;
use Illuminate\Database\Eloquent\Model;

class AssignmentService
{
    public function __construct(protected InspectionAssignment $inspectionAssignment) {}

    public function setSubject(Inspection $inspection, Model $subject): InspectionAssignment|null
    {       
        $inspectionAssignment = $this->inspectionAssignment
            ->where('inspection_id', '=', $inspection->id)
            ->first();
        
        // update
        if ($inspectionAssignment !== null) {
            $inspectionAssignment->subject()->associate($subject);
            $inspectionAssignment->save();
        }
        // create
        else {
            $inspectionAssignment = new InspectionAssignment();
            $inspectionAssignment->inspection()->associate($inspection);
            $inspectionAssignment->subject()->associate($subject);
            $inspectionAssignment->save();
        }

        return $this->inspectionAssignment;
    }

    public function getSubject(Inspection $inspection): Model|null
    {        
        return $this->inspectionAssignment
            ->with('subject')
            ->where('inspection_id', '=', $inspection->id)
            ->first()
            ?->subject;
    }

    public function update(Model $model, array $data): Model|null
    {        
        $model->subject_id = $data['subject_id'];
        $model->inspection->date = $data['inspection_date'];

        $model->push();
        return $model;
    }    
}
