<?php

namespace App\StateTransitions\TS\Ticket;

use App\States\TS\Ticket\Created;
use App\States\TS\Ticket\InProgress;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\ModelStates\Transition;

class CreatedToInProgress extends Transition
{
    public function __construct(private Ticket $ticket, private Authenticatable $user) {}

    public function canTransition(): bool
    {
        $userCan = app()->runningInConsole() ? true : ($this->user->can('cancel-ticket') || $this->user->hasRole('super-admin'));
        $validInitialState = $this->ticket->state->equals(Created::class);
        return $userCan && $validInitialState;
    }

    public function handle(): ?Ticket
    {
        if ($this->canTransition()) {

            $this->ticket->state = new InProgress($this->ticket);
            $this->ticket->save();

            return $this->ticket;
        }
        return null;
    }
}
