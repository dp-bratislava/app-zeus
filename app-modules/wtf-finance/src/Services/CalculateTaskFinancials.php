<?php

namespace Dpb\Modules\WtfFinance\Services;

use Dpb\Packages\WtfFinance\Filament\Services\FinancialRecordWriter;
use Dpb\Modules\WtfFinance\Filament\Services\PricingBasisResolver;
use Dpb\Modules\WtfFinance\Filament\Services\PricingGroupResolver;
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

        $groups = $this->pricingGroups
            ->groupActivities($activities);

        foreach ($groups as $group) {
            $this->calculateGroup(
                $task,
                $group->pricingGroup,
                $group->activities,
            );
        }
    }

    private function calculateGroup(
        Task $task,
        PricingGroup $pricingGroup,
        Collection $activities,
    ): void {
        $calculator = $this->calculators->for(
            $pricingGroup->rule
        );

        // ...
    }
}