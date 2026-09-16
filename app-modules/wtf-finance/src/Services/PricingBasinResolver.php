<?php

namespace Dpb\Modules\WtfFinance\Filament\Services;

use Dpb\Packages\WtfFinance\DTO\PricingBasis;
use Dpb\Packages\WtfFinance\Exceptions\UnsupportedPricingRuleException;
use Dpb\Packages\WtfFinance\Models\PricingGroup;
use Dpb\WorkTimeFund\Models\ActivityRecord;

final class PricingBasisResolver
{
    public function resolve(
        ActivityRecord $activity,
        PricingGroup $group
    ): PricingBasis {
        $rule = $group->rule;

        return match ($group->rule->code) {
            // 'per_unit' => PricingBasis::from(
            //     $activity->quantity,
            //     'unit'
            // ),

            // 'per_duration' => PricingBasis::from(
            //     $activity->real_duration,
            //     $group->rule->dimension->unit
            // ),

            // @TODO
            'per_seat' => PricingBasis::from(
                // subject seats count
                20,
                $group->rule->dimension->unit
            ),

            default => throw new UnsupportedPricingRuleException(
                $rule->code
            ),
        };
    }
}