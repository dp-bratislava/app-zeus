<?php

namespace App\States\Incident;

use App\States\Incident\IncidentState;

class Created extends IncidentState
{
    public static $name = "created";

    public function label(): string
    {
        return __('incidents/incident.states.created');
    }
}
