<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ActivityData extends Data
{
    public function __construct(
        public int $id,
        public ActivityTemplateData $template,
        #[DataCollectionOf(WorkIntervalData::class)]
        public DataCollection $workAssignments,
        // #[DataCollectionOf(UnitRateData::class)]
        // public array $unitRates = [],
    ) {}
}
