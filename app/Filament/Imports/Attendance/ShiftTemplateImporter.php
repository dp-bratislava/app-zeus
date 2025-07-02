<?php

namespace App\Filament\Imports\Attendance;

use App\Models\Attendance\ShiftTemplate;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class ShiftTemplateImporter extends Importer
{
    protected static ?string $model = ShiftTemplate::class;

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
            ImportColumn::make('time_from')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('time_to')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('duration')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(function (?string $state): ?int {
                    if (blank($state)) {
                        return null;
                    }

                    $parts = explode(":", $state);
                    return ((int) $parts[0]) * 60 + ((int) $parts[1]);
                }),
        ];
    }

    public function resolveRecord(): ?ShiftTemplate
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new ShiftTemplate();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your shift template import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
