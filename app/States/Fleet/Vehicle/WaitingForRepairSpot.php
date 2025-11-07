<?php

namespace App\States\Fleet\Vehicle;

 class WaitingForRepairSpot extends VehicleState
{
    public static $name = "waiting-for-repair-spot";

    public function label():string {
        return __('fleet/vehicle.states.waiting-for-repair-spot');
    }
}