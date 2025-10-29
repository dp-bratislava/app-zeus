<?php

namespace App\States\Fleet\Vehicle;

 class WaitingForInsurance extends VehicleState
{
    public static $name = "waiting-for-insurance";

    public function label():string {
        return __('fleet/vehicle.states.waiting-for-insurance');
    }
}