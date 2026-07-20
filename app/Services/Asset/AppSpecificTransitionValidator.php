<?php
namespace App\Services\Asset;

use Dpb\Package\Assets\Contracts\TransitionValidatorInterface;
use Dpb\Package\Assets\Enums\AssetState;
use Illuminate\Database\Eloquent\Model;

class AppSpecificTransitionValidator implements TransitionValidatorInterface
{
    public function validate(Model $asset, AssetState $from, AssetState $to): bool
    {
        return match ([$from, $to]) {
            [AssetState::DIEL_NA_VOZE, AssetState::NA_VYRADENIE_NESCHVALENE] 
                => $this->canScrapFromVehicle($asset),

            [AssetState::VYZISK, AssetState::NA_VYRADENIE_NESCHVALENE] 
                => $this->canScrapFromRecovery($asset),

            default => true,
        };
    }

    private function canScrapFromVehicle(Model $asset): bool
    {
        // Put application-specific business logic here
        // (e.g., check user permissions, vehicle status, etc.)
        return true;
    }

    private function canScrapFromRecovery(Model $asset): bool
    {
        // Put application-specific business logic here
        return true;
    }
}