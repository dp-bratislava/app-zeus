<?php

namespace Dpb\Modules\WtfFinance\Calculators;

use Dpb\Packages\WtfFinance\Contracts\PricingCalculator;
use Dpb\Packages\WtfFinance\DTO\CalculationResult;
use Dpb\Packages\WtfFinance\DTO\PricingBasis;
use Dpb\Packages\WtfFinance\Models\PricingGroup;

final class PerMeterCalculator implements PricingCalculator
{
    public function calculate(
        PricingGroup $group,
        PricingBasis $basis
    ): CalculationResult {
        return new CalculationResult(
            quantity: $basis->value,
            unitPrice: $group->unit_price,
            amount: $basis->value * $group->unit_price,
            basisValue: $basis->value,
            basisUnit: $basis->unit,
        );
    }
}