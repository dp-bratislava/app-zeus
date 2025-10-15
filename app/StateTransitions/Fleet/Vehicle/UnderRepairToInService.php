<?php

namespace App\StateTransitions\Fleet\Vehicle;

use Dpb\Package\Fleet\Models\Vehicle;
use App\States\Fleet\Vehicle\InService;
use App\States\Fleet\Vehicle\UnderRepair;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\ModelStates\Transition;

class UnderRepairToInService extends Transition
{
    public function __construct(private Vehicle $vehicle, private Authenticatable $user) {}

    public function canTransition(): bool
    {
        // $userCan = app()->runningInConsole() ? true : ($this->user->can('discard-vehicle') || $this->user->hasRole('super-admin'));
        $userCan = true;
        $validInitialState = $this->vehicle->state->equals(UnderRepair::class);
        return $userCan && $validInitialState;
    }

    public function handle(): ?Vehicle
    {
        if ($this->canTransition()) {

            $this->vehicle->state = new InService($this->vehicle);
            $this->vehicle->save();

            return $this->vehicle;
        }
        return null;
    }
}
