<?php

namespace App\States\Fleet\Vehicle;

 class UnderRepair extends VehicleState
{
    public static $name = "under-repair";

    public function label():string {
        return __('fleet/vehicle.states.under-repair');
    }
}