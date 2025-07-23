<?php

namespace App\Filament\Imports\Fleet;

use App\Models\Fleet\Vehicle;
use App\Models\Fleet\VehicleGroup;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class VehicleGroupImporter extends Importer
{
    protected static ?string $model = VehicleGroup::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code'),
            ImportColumn::make('title'),
            ImportColumn::make('description'),
        ];
    }

    public function resolveRecord(): ?VehicleGroup
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new VehicleGroup();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your vehicle group import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
