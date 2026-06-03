<?php

namespace App\Services;

use Carbon\Carbon;

class DateRangeValidator
{
    public function __construct(
        private int $maxDays = 120,
        private string $dateField = 'activity_date'
    ) {}

    public function validate(?string $from, ?string $to): array
    {
        return [
            'isValid' => !$this->isExceeded($from, $to),
            'error' => $this->getErrorMessage($from, $to),
            'daysDifference' => $this->getDaysDifference($from, $to),
        ];
    }

    public function isExceeded(?string $from, ?string $to): bool
    {
        if (!$from || !$to) {
            return false;
        }

        try {
            return $this->getDaysDifference($from, $to) > $this->maxDays;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getDaysDifference(?string $from, ?string $to): int
    {
        if (!$from || !$to) {
            return 0;
        }

        try {
            return Carbon::parse($from)->diffInDays(Carbon::parse($to));
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getErrorMessage(?string $from, ?string $to): string
    {
        if ($this->isExceeded($from, $to)) {
            return "Rozsah dátumov nesmie byť väčší ako {$this->maxDays} dní.";
        }

        return '';
    }
}
