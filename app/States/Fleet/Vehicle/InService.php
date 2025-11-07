<?php

namespace App\States\Fleet\Vehicle;

 class InService extends VehicleState
{
    public static $name = "in-service";

    public function label():string {
        return __('fleet/vehicle.states.in-service');
    }
}