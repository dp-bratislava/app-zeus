<?php

namespace App\Enums;

use Dpb\Package\Assets\Contracts\AssetStateInterface;
use Dpb\Package\Assets\Contracts\MovementTypeInterface;

enum MovementType: string implements MovementTypeInterface
{
    case MONTAZ = 'montaz';
    case DEMONTAZ = 'demontaz';

    public function value(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::MONTAZ => 'Montáž',
            self::DEMONTAZ => 'Demontáž',
        };
    }

    public function allowedStartingStates(): array
    {
        return match ($this) {
            // "Montáž" typically moves a spare part from storage/prep to the vehicle
            self::MONTAZ => [
                AssetState::VYDAJ_NA_MONTAZ,
                AssetState::VYZISK,
            ],
            // "Demontáž" typically removes a part from the vehicle to a storage or repair pipeline
            self::DEMONTAZ => [
                AssetState::DIEL_NA_VOZE,
            ],
        };
    }

    public function allowedEndingStates(): array
    {
        return match ($this) {
            self::MONTAZ => [
                AssetState::DIEL_NA_VOZE,
            ],
            self::DEMONTAZ => [
                AssetState::NA_VYRADENIE_NESCHVALENE,
                AssetState::VYZISK,
                AssetState::NA_OPRAVU_NEZARUCNA,
                AssetState::NA_OPRAVU_ZARUCNA,
                AssetState::NA_OPRAVU_REKLAMACIA,
            ],
        };
    }

    public function assetLosesSlot(): bool
    {
        return match ($this) {
            self::MONTAZ => false,
            self::DEMONTAZ => true,
        };
    }

    public function isValidTransition(AssetStateInterface $from, AssetStateInterface $to): bool
    {
        return in_array($from, $this->allowedStartingStates(), true) 
            && in_array($to, $this->allowedEndingStates(), true);
    }
}
