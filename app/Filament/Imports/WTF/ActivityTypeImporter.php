<?php

namespace App\Filament\Imports\WTF;

use App\Models\WTF\ActivityType;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class ActivityTypeImporter extends Importer
{
    protected static ?string $model = ActivityType::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return "";
                    }

                    return Str::trim($state);
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
            ImportColumn::make('description')
                // ->requiredMapping()
                ->rules(['max:255'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return "";
                    }

                    return Str::trim($state);
                }),                
        ];
    }

    public function resolveRecord(): ?ActivityType
    {
        return ActivityType::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'code' => $this->data['code'],
        ]);

        // return new ActivityType();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your activity type import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
