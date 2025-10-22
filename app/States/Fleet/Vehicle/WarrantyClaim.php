<?php

namespace App\States\Fleet\Vehicle;

 class WarrantyClaim extends VehicleState
{
    public static $name = "warranty-claim";

    public function label():string {
        return __('fleet/vehicle.states.warranty-claim');
    }
}