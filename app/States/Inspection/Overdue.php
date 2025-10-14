<?php

namespace App\States\Inspection;

use App\States\Inspection\InspectionState;

 class Overdue extends InspectionState
{
    public static $name = "overdue";

    public function label():string {
        return __('inspections/inspection.states.overdue');
    }    
}