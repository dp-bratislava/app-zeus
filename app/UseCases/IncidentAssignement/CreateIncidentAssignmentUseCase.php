<?php

namespace App\UseCases\IncidentAssignement;

use App\Data\Incident\IncidentAssignmentData;
use App\Data\Incident\IncidentData;
use App\Models\IncidentAssignment;
use App\Services\IncidentAssignmentService;
use App\States;
use App\UseCases\TicketAssignment\CreateFromIncidentUseCase;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Carbon;

class CreateIncidentAssignmentUseCase
{
    public function __construct(
        private IncidentAssignmentService $incidentAssignmentSvc,
        private CreateFromIncidentUseCase $createTicketAssignmentUseCase,
        private Guard $guard,
    ) {}

    public function execute(array $data): ?IncidentAssignment
    {
        // incident item
        $incidentData = new IncidentData(
            null,
            Carbon::parse($data['date']),
            $data['description'] ?? null,
            $data['type_id'],
            States\Incident\Created::$name,
        );

        // create incident assignment
        $taData = new IncidentAssignmentData(
            null,
            $incidentData,
            $data['subject_id'],
            'vehicle',
            $this->guard->id(),
        );

        // create incident
        $incident = $this->incidentAssignmentSvc->create($taData);

        // crete ticket assignment
        $this->createTicketAssignmentUseCase->execute($incident);

        return $incident;
    }
}
