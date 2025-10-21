<?php

namespace App\Services\Inspection;

use App\Models\InspectionTemplateAssignment;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TemplateAssignmentService
{
    public function __construct(protected InspectionTemplateAssignment $inspectionTemplateAssignment) {}

    public function setSubject(InspectionTemplate $inspectionTemplate, Model $subject): InspectionTemplateAssignment|null
    {       
        $inspectionTemplateAssignment = $this->inspectionTemplateAssignment
            ->where('template_id', '=', $inspectionTemplate->id)
            ->first();
        
        // update
        if ($inspectionTemplateAssignment !== null) {
            $inspectionTemplateAssignment->subject()->associate($subject);
            $inspectionTemplateAssignment->save();
        }
        // create
        else {
            $inspectionTemplateAssignment = new InspectionTemplateAssignment();
            $inspectionTemplateAssignment->template()->associate($inspectionTemplate);
            $inspectionTemplateAssignment->subject()->associate($subject);
            $inspectionTemplateAssignment->save();
        }

        return $this->inspectionTemplateAssignment;
    }

    public function getSubjectsByTemplate(InspectionTemplate $inspectionTemplate, array|null $subjectTypes): Collection
    {        
        return $this->inspectionTemplateAssignment
            ->with('subject')
            ->where('template_id', '=', $inspectionTemplate->id)
            ->when($subjectTypes != null, function($q, $subjectTypes) {
                return $q->whereIn('subject_type', $subjectTypes);
            })
            ->get()
            ->map(fn($assignment) => $assignment->subject);
    }

    public function getTemplatesBySubject(Model $subject): Collection
    {        
        return $this->inspectionTemplateAssignment
            ->with('template')
            ->where('subject_id', '=', $subject->id)
            ->where('subject_type', '=', $subject->getMorphClass())
            ->get()
            ->map(fn($assignment) => $assignment->template);
    }    
}
