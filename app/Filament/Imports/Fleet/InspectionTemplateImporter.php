<?php

namespace App\Filament\Imports\Fleet;

use Dpb\Packages\Vehicles\Models\Inspection\InspectionTemplate;
use Dpb\Packages\Vehicles\Models\Vehicle;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class InspectionTemplateImporter extends Importer
{
    protected static ?string $model = InspectionTemplate::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title'),
            ImportColumn::make('distance_interval'),
            ImportColumn::make('distance_first_advance'),
            ImportColumn::make('distance_second_advance'),
            ImportColumn::make('time_interval'),
            ImportColumn::make('time_first_advance'),
            ImportColumn::make('time_second_advance'),
            ImportColumn::make('is_one_time')
                ->castStateUsing(function (?string $state): ?bool {
                    return $state === 1;
                }),
            // ImportColumn::make('task_group')
            // ->relationship('taskGroup', 'title'),
            ImportColumn::make('note'),
            ImportColumn::make('is_periodical')
                ->castStateUsing(function (?string $state): ?bool {
                    return $state === 1;
                }),
        ];
    }

    public function resolveRecord(): ?InspectionTemplate
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new InspectionTemplate();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your inspection template import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
