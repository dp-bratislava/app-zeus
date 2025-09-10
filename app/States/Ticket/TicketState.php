<?php

namespace App\States\Ticket;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * @extends State<\App\Models\TS\Ticket>
 */
abstract class TicketState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Created::class)
            ->allowTransition(Created::class, InProgress::class)
            ->allowTransition(InProgress::class, Closed::class)
            ->allowTransition(InProgress::class, Cancelled::class)
        ;
    }
}
