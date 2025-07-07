<?php

namespace App\Filament\Imports\WTF;

use App\Models\WTF\StandardisedActivityGroup;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class StandardisedActivityGroupImporter extends Importer
{
    protected static ?string $model = StandardisedActivityGroup::class;

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
            ImportColumn::make('description')
                ->rules(['max:255']),
            ImportColumn::make('department')
                ->relationship('department', 'code')
                // ->rules(['required', 'max:255']),
                ->rules(['max:255']),                                       
        ];
    }

    public function resolveRecord(): ?StandardisedActivityGroup
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new StandardisedActivityGroup();
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
