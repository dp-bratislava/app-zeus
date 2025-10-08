<?php

namespace App\States\Fleet\Vehicle;

use App\StateTransitions\Fleet\Vehicle\DiscardedToInService;
use App\StateTransitions\Fleet\Vehicle\InServiceToDiscarded;
use Dpb\Package\Fleet\States\VehicleState as BaseVehicleState;
use Spatie\ModelStates\StateConfig;

abstract class VehicleState extends BaseVehicleState
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(InService::class)
            ->allowTransition(InService::class, Discarded::class, InServiceToDiscarded::class)
            ->allowTransition(Discarded::class, InService::class, DiscardedToInService::class)
        ;
    }
}
