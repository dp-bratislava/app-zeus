<?php

namespace App\Filament\Components;

use Filament\Tables\Columns\TextColumn;
use Carbon\CarbonInterval;

class DurationColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set the default label (optional)
        $this->label('Duration');

        // Apply your custom formatting logic
        $this->formatStateUsing(static function ($state) {
            if (!$state) {
                return '0:00';
            }

            $interval = CarbonInterval::seconds($state)->cascade();
            
            // Using totalHours to ensure values > 24h are displayed correctly as hours
            $hours = floor($interval->totalHours);
            $minutes = $interval->minutes;

            return sprintf('%d:%02d', $hours, $minutes);
        });
    }
}