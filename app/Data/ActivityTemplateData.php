<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ActivityTemplateData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public int $duration,
        public bool $isCatalogised,
        public int $people,
        // #[DataCollectionOf(ExpenseData::class)]
        // public array $expenses = [],
        // #[DataCollectionOf(UnitRateData::class)]
        // public array $unitRates = [],
    ) {}
}
