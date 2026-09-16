<?php

namespace Dpb\Modules\WtfFinance\Services;

use Dpb\Modules\WtfFinance\Calculators\PerMeterCalculator;
use Dpb\Modules\WtfFinance\Calculators\PerSeatCalculator;
use Dpb\Packages\WtfFinance\Contracts\PricingCalculator;
use Dpb\Packages\WtfFinance\Exceptions\UnsupportedPricingRuleException;
use Dpb\Packages\WtfFinance\Models\PricingRule;

final class PricingCalculatorFactory
{
    public function for(PricingRule $rule): PricingCalculator
    {
        return match ($rule->code) {
            'per_meter' => app(PerMeterCalculator::class),
            'per_seat' => app(PerSeatCalculator::class),
            // 'per_unit' => app(PerUnitCalculator::class),
            // // 'per_duration' => app(PerDurationCalculator::class),
            // // 'subject_measure' => app(SubjectMeasureCalculator::class),
            // 'fixed' => app(FixedCalculator::class),

            default => throw new UnsupportedPricingRuleException(
                $rule->code
            ),
        };
    }
}