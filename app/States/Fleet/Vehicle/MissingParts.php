<?php

namespace App\States\Fleet\Vehicle;

 class MissingParts extends VehicleState
{
    public static $name = "missing-parts";

    public function label():string {
        return __('fleet/vehicle.states.missing-parts');
    }
}