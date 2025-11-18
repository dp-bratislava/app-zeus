<?php

namespace App\Services;

use App\Models\IncidentAssignment;
use App\States;
use Dpb\Package\Incidents\Models\Incident;
use Illuminate\Contracts\Auth\Guard;

class IncidentAssignmentRepository
{
    protected const SUBJECT_TYPE = 'vehicle';

    public function __construct(
        protected Incident $incidentModel,
        protected IncidentAssignment $incidentAssignment,
        protected Guard $guard
    ) {}

    public function create(array $data): IncidentAssignment
    {
        // set default state
        $incidentData = $data['incident'] ?? States\Incident\Created::$name;
        $incidentData['state'] = $data['state'] ?? States\Incident\Created::$name;

        // create incident
        $incident = $this->incidentModel->create($incidentData);

        
        // create incident assigment
        $incidentAssignemnt = $this->incidentAssignment->newInstance();
        $incidentAssignemnt->incident()->associate($incident);
        // subject
        $incidentAssignemnt->subject_id = $data['subject_id'];
        $incidentAssignemnt->subject_type = self::SUBJECT_TYPE;

        $incidentAssignemnt->author()->associate($this->guard->user());
        $incidentAssignemnt->save();

        return $incidentAssignemnt;
    }

    public function update(IncidentAssignment $incidentAssignment, array $data): IncidentAssignment
    {
        // set default state
        $incidentData = $data['incident'] ?? States\Incident\Created::$name;
        $incidentData['state'] = $data['state'] ?? States\Incident\Created::$name;

        // update incident
        $incidentAssignment->incident->update($incidentData);

        // subject
        $incidentAssignment->subject_id = $data['subject_id'];
        $incidentAssignment->subject_type = self::SUBJECT_TYPE;
        $incidentAssignment->save();

        return $incidentAssignment;
    }

    // protected function getSubject(array $data) {
    //     return Vehicle::find($data)
    // }
}
