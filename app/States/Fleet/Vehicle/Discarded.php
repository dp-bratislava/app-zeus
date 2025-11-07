<?php

namespace App\States\Fleet\Vehicle;

 class Discarded extends VehicleState
{
    public static $name = "discarded";

    public function label():string {
        return __('fleet/vehicle.states.discarded');
    }    
}