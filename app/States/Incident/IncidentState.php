<?php

namespace App\States\Incident;

use Dpb\Package\Incidents\States\IncidentState as BaseIncidentState;
use Spatie\ModelStates\StateConfig;

abstract class IncidentState extends BaseIncidentState
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Created::class)
        ;
    }
}
