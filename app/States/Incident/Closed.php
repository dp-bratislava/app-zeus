<?php

namespace App\States\Incident;

use App\States\Incident\IncidentState;

 class Closed extends IncidentState
{
    public static $name = "closed";

    public function label():string {
        return __('incidents/incident.states.closed');
    }    
}