<?php

namespace App\States\Inspection;

use App\States\Inspection\InspectionState;

 class InProgress extends InspectionState
{
    public static $name = "in-progress";

    public function label():string {
        return __('inspections/inspection.states.in-progress');
    }    
}