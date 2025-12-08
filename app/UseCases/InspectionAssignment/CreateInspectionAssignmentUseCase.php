<?php

namespace App\UseCases\InspectionAssignment;

use App\Data\Inspection\InspectionAssignmentData;
use App\Data\Inspection\InspectionData;
use App\Models\InspectionAssignment;
use App\Services\InspectionAssignmentService;
use Illuminate\Contracts\Auth\Guard;
use App\States;
use Illuminate\Support\Carbon;

class CreateInspectionAssignmentUseCase
{
    public function __construct(
        private InspectionAssignmentService $inspectionAssignmentSvc,
        // private CreateFromInspectionUseCase $createTicketAssignmentUseCase,
        private Guard $guard,
    ) {}

    public function execute(array $data): ?InspectionAssignment
    {
        // inspection item
        $inspectionData = new InspectionData(
            null,
            Carbon::parse($data['date']),
            $data['template_id'] ?? null,
            States\Inspection\Upcoming::$name,
        );
        
        // create inspection assignment
        $iaData = new InspectionAssignmentData(
            null,
            $inspectionData,
            $data['subject_id'],
            'vehicle',
            // $this->guard->id(),
        );

        // create inspection
        return $this->inspectionAssignmentSvc->create($iaData);
    }
}
