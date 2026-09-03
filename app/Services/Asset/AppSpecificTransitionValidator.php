<?php

namespace App\Services\Asset;

use Dpb\Package\Assets\Contracts\AssetStateInterface;
use Dpb\Package\Assets\Contracts\TransitionValidatorInterface;
use Dpb\WtfTmsBridge\Enums\AssetState;
use Illuminate\Database\Eloquent\Model;

class AppSpecificTransitionValidator implements TransitionValidatorInterface
{
    public function validate(Model $asset, AssetStateInterface $from, AssetStateInterface $to): bool
    {
        if (! ($from instanceof AssetState && $to instanceof AssetState)) {
            return true;
        }

        return match (true) {
            $from === AssetState::DIEL_NA_VOZE && $to === AssetState::NA_VYRADENIE => $this->canScrapFromVehicle($asset),

            $from === AssetState::VYZISK && $to === AssetState::NA_VYRADENIE => $this->canScrapFromRecovery($asset),

            default => true,
        };
    }

    private function canScrapFromVehicle(Model $asset): bool
    {
        // Put application-specific business logic here
        return true;
    }

    private function canScrapFromRecovery(Model $asset): bool
    {
        // Put application-specific business logic here
        return true;
    }
}
