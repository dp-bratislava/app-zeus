<?php

namespace App\Filament\Imports\WTF;

use App\Models\WTF\StandardisedActivityTemplate;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class StandardisedActivityTemplateImporter extends Importer
{
    protected static ?string $model = StandardisedActivityTemplate::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return "";
                    }

                    return Str::trim($state);
                }),
            ImportColumn::make('duration')
                ->requiredMapping()
                ->rules(['required', 'integer']),
            ImportColumn::make('is_divisible')
                ->requiredMapping()
                ->rules(['required', 'boolean']), 
            ImportColumn::make('people')
                ->requiredMapping()
                ->rules(['required', 'integer']),
            ImportColumn::make('group')
                ->relationship('group', 'title')
                ->requiredMapping()
                ->rules(['required', 'max:255'])                 
        ];
    }

    public function resolveRecord(): ?StandardisedActivityTemplate
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new StandardisedActivityTemplate();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your standardised activity template import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
