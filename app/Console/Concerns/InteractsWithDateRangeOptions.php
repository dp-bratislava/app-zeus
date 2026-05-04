<?php

namespace App\Console\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

trait InteractsWithDateRangeOptions
{
    protected function resolveDateRange(): array
    {
        $input = [
            'from' => $this->option('from'),
            'to'   => $this->option('to'),
        ];

        Validator::make($input, [
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ])->validate();

        $from = $input['from']
            ? Carbon::parse($input['from'])->startOfSecond()
            : null;

        $to = $input['to']
            ? Carbon::parse($input['to'])->endOfSecond()
            : null;

        // prevent conflicting options
        if ($this->option('all') && ($from || $to)) {
            throw new \InvalidArgumentException(
                '--all cannot be combined with --from or --to'
            );
        }

        return [$from, $to];
    }

    protected function isFullSync(): bool
    {
        return (bool) $this->option('all');
    }

    protected function isForced(): bool
    {
        return (bool) $this->option('force');
    }

    protected function logDateRange(?Carbon $from, ?Carbon $to): void
    {
        $this->info(sprintf(
            'Range → from: %s | to: %s | all: %s',
            $from?->toDateTimeString() ?? 'auto',
            $to?->toDateTimeString() ?? 'auto',
            $this->isFullSync() ? 'yes' : 'no'
        ));
    }
}