<?php

namespace App\Filament\Imports\Attendance;

use App\Models\Attendance\CalendarGroup;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class CalendarGroupImporter extends Importer
{
    protected static ?string $model = CalendarGroup::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(function (?string $state): ?string {
                    return (blank($state)) ? "" : $state;
                }),                
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return "";
                    }

                    return Str::trim($state);
                }),
        ];
    }

    public function resolveRecord(): ?CalendarGroup
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new CalendarGroup();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your calendar group import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
