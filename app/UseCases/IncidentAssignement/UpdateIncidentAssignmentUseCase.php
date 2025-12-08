<?php

namespace App\UseCases\IncidentAssignement;

use App\Data\Incident\IncidentAssignmentData;
use App\Data\Incident\IncidentData;
use App\Models\IncidentAssignment;
use App\Services\IncidentAssignmentService;
use Illuminate\Contracts\Auth\Guard;
use App\States;
use App\UseCases\TicketAssignment\CreateFromIncidentUseCase;
use Illuminate\Support\Carbon;

class UpdateIncidentAssignmentUseCase
{
    public function __construct(
        private IncidentAssignmentService $incidentAssignmentSvc,
        // private CreateFromIncidentUseCase $createTicketAssignmentUseCase,
        private Guard $guard,
    ) {}

    public function execute(IncidentAssignment $incidentAssignment, array $data): ?IncidentAssignment
    {
        // incident item
        $incidentData = new IncidentData(
            $incidentAssignment->incident->id,
            Carbon::parse($data['date']),
            $data['description'] ?? null,
            $data['type_id'],
            States\Incident\Created::$name,
        );
        
        // create incident assignment
        $iaData = new IncidentAssignmentData(
            $incidentAssignment->id,
            $incidentData,
            $data['subject_id'],
            'vehicle',
            $this->guard->id(),
        );

        // create incident
        $incident = $this->incidentAssignmentSvc->update($iaData);

        // // crete ticket assignment
        // $this->createTicketAssignmentUseCase->execute($incident);

        return $incident;
    }
}
