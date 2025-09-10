<?php

namespace App\StateTransitions\Ticket;

use App\Models\TS\Ticket;
use App\States\Ticket\Cancelled;
use App\States\Ticket\InProgress;
use Illuminate\Contracts\Auth\Guard;
use Spatie\ModelStates\Transition;

class InProgressToCancelled extends Transition
{
    public function __construct(private Ticket $ticket, private Guard $guard)
    {
    }

    public function canTransition(): bool
    {
        $userCan = $this->guard->user()->can('cancel-ticket');
        $validInitialState = $this->ticket->state->equals(InProgress::class);
        return $userCan && $validInitialState;
    }

    public function handle(): ?Ticket
    {
        if ($this->canTransition()) {

            $this->ticket->state = new Cancelled($this->ticket);
            $this->ticket->save();
            
            return $this->ticket;
        }
        return null;
    }
}