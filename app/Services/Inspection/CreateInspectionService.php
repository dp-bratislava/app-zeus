<?php

namespace App\Services\Inspection;

use App\Models\InspectionAssignment;
use App\States;
use Carbon\Carbon;
use Dpb\Package\Inspections\Models\Inspection;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;

class CreateInspectionService
{
    public function __construct(
        protected ConnectionInterface $db,
        protected Inspection $inspection,
        protected AssignmentService $assignmentSvc,
        protected InspectionAssignment $inspectionAssignment,
    ) {}

    public function create(Model $subject, InspectionTemplate $template): Inspection|null
    {
        $this->db->transaction(function () use ($subject, $template) {                    
            // create inspection
            $data = [
                'date' => Carbon::now(),
                'template_id' => $template->id,
                'state' => States\Inspection\Upcoming::$name,
            ];
            
            $inspection = $this->inspection->create($data);

            // add inspection subject relation            
            $this->assignmentSvc->setSubject($inspection, $subject);

            return $inspection;
        });

        return null;
    }
}
