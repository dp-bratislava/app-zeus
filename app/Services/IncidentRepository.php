<?php

namespace App\Services;

use App\Models\IncidentAssignment;
use App\States;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Incidents\Models\Incident;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Relations\Relation;

class IncidentRepository
{
    protected const SUBJECT_TYPE = 'vehicle';

    public function __construct(
        protected Incident $incident,
        protected IncidentAssignment $incidentAssignment,
        // protected Relation $relation,
        protected Guard $guard
    ) {}

    public function create(array $data)
    {
        // set default state
        $data['state'] = $data['state'] ?? States\Incident\Created::$name;

        // create incident
        $incident = $this->incident->create($data);

        // subject
        // $subject = $this->relation->getMorphedModel('vehicle')::find($data['subject_id']);

        // create incident assigment
        $incidentAssignemnt = $this->incidentAssignment->newInstance();
        $incidentAssignemnt->incident()->associate($incident);
        $incidentAssignemnt->subject_id = $data['subject_id'];
        $incidentAssignemnt->subject_type = self::SUBJECT_TYPE;

        // $incidentAssignemnt->subject()->associate(Vehicle::find($data['subject_id']));
        // $incidentAssignemnt->subject()->associate($subject);
        $incidentAssignemnt->author()->associate($this->guard->user());
        $incidentAssignemnt->save();

        return $incident;
    }

    public function update(Incident $incident, array $data)
    {
        // set default state
        $data['state'] = $data['state'] ?? States\Incident\Created::$name;

        // create incident
        $incident->update($data);

        // subject
        // $subject = $this->relation->getMorphedModel('vehicle')::find($data['subject_id']);

        // create incident assigment
        $incidentAssignemnt = $this->incidentAssignment->whereBelongsTo($incident)->first();
        $incidentAssignemnt->incident()->associate($incident);
        $incidentAssignemnt->subject_id = $data['subject_id'];
        $incidentAssignemnt->subject_type = self::SUBJECT_TYPE;

        // $incidentAssignemnt->subject()->associate(Vehicle::find($data['subject_id']));
        // $incidentAssignemnt->subject()->associate($subject);
        // $incidentAssignemnt->author()->associate($this->guard->user());
        $incidentAssignemnt->save();

        return $incident;
    }

    // protected function getSubject(array $data) {
    //     return Vehicle::find($data)
    // }
}
