<?php

namespace App\States\Inspection;

use App\States\Inspection\InspectionState;

 class Upcoming extends InspectionState
{
    public static $name = "upcoming";

    public function label():string {
        return __('inspections/inspection.states.upcoming');
    }    
}