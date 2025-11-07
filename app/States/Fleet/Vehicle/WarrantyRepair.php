<?php

namespace App\States\Fleet\Vehicle;

 class WarrantyRepair extends VehicleState
{
    public static $name = "Warranty-repair";

    public function label():string {
        return __('fleet/vehicle.states.warranty-repair');
    }
}