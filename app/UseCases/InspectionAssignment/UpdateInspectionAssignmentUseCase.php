<?php

namespace App\UseCases\InspectionAssignment;

use App\Data\Inspection\InspectionAssignmentData;
use App\Data\Inspection\InspectionData;
use App\Models\InspectionAssignment;
use App\Services\InspectionAssignmentService;
use Illuminate\Contracts\Auth\Guard;
use App\States;
use App\UseCases\TicketAssignment\CreateFromInspectionUseCase;
use Illuminate\Support\Carbon;

class UpdateInspectionAssignmentUseCase
{
    public function __construct(
        private InspectionAssignmentService $inspectionAssignmentSvc,
        // private CreateFromInspectionUseCase $createTicketAssignmentUseCase,
        private Guard $guard,
    ) {}

    public function execute(InspectionAssignment $inspectionAssignment, array $data): ?InspectionAssignment
    {
        // inspection item
        $inspectionData = new InspectionData(
            $inspectionAssignment->inspection->id,
            Carbon::parse($data['date']),
            $data['template_id'] ?? null,
            States\Inspection\Upcoming::$name,
        );
        
        // create inspection assignment
        $iaData = new InspectionAssignmentData(
            $inspectionAssignment->id,
            $inspectionData,
            $data['subject_id'],
            'vehicle',
            // $this->guard->id(),
        );

        return $this->inspectionAssignmentSvc->update($iaData);
    }
}
