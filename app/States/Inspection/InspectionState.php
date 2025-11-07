<?php

namespace App\States\Inspection;

use App\States\Inspection\Upcoming;
use Dpb\Package\Inspections\States\InspectionState as BaseInspectionState;
use Spatie\ModelStates\StateConfig;

abstract class InspectionState extends BaseInspectionState
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Upcoming::class)
            // ->allowTransition(Created::class, InProgress::class, CreatedToInProgress::class)
            // ->allowTransition(InProgress::class, Closed::class)
            // ->allowTransition(InProgress::class, Cancelled::class, InProgressToCancelled::class)
        ;
    }
}
