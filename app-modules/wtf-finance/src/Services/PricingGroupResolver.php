<?php

namespace Dpb\Modules\WtfFinance\Filament\Services;

use Dpb\Packages\WtfFinance\Models\PricingGroup;
use Dpb\WorkTimeFund\Models\ActivityRecord;

final class PricingGroupResolver
{
    public function resolve(ActivityRecord $activity): ?PricingGroup
    {
        return PricingGroup::query()
            ->whereHas('operations', function ($query) use ($activity) {
                $query->where(
                    'dpb_worktimefund_model_operation.id',
                    $activity->operation_id
                );
            })
            ->where('is_active', true)
            ->whereDate('valid_from', '<=', $activity->date)
            ->where(function ($query) use ($activity) {
                $query
                    ->whereNull('valid_to')
                    ->orWhereDate('valid_to', '>=', $activity->date);
            })
            ->first();
    }
}