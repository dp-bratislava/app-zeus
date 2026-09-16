<?php

namespace Dpb\Packages\WtfFinance\Services;

use Dpb\Packages\WtfFinance\Filament\Services\FinancialRecordWriter;
use Dpb\Packages\WtfFinance\Filament\Services\PricingBasisResolver;
use Dpb\Packages\WtfFinance\Filament\Services\PricingGroupResolver;
use Dpb\WorkTimeFund\Models\Task;

final class CalculateTaskFinancials
{
    public function __construct(
        private PricingGroupResolver $pricingGroups,
        private PricingBasisResolver $basisResolver,
        private PricingCalculatorFactory $calculators,
        private FinancialRecordWriter $writer,
    ) {}

    public function execute(Task $task): void
    {
        $activities = $task->activityRecords()
            ->with(['operation', 'maintainable'])
            ->get();

        $groups = $this->groupByPricingGroup($activities);

        foreach ($groups as $group => $groupActivities) {
            $calculator = $this->calculators->for(
                $group->rule
            );

            $calculations = [];

            foreach ($groupActivities as $activity) {
                $basis = $this->basisResolver->resolve(
                    $activity,
                    $group
                );

                $calculations[] = $calculator->calculate(
                    $group,
                    $basis
                );
            }

            $this->writer->write(
                task: $task,
                pricingGroup: $group,
                activities: $groupActivities,
                calculations: $calculations,
            );
        }
    }
}