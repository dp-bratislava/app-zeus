<?php

namespace App\Filament\Imports\Fleet;

use App\Models\Fleet\Vehicle;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class VehicleImporter extends Importer
{
    protected static ?string $model = Vehicle::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code'),
            ImportColumn::make('model')
                ->relationship('model', 'title')
            // ->rules(['required', 'max:255']),
                ->rules(['max:255']),
            ImportColumn::make('end_of_warranty'),
            ImportColumn::make('licence_plate'),
        ];
    }

    public function resolveRecord(): ?Vehicle
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Vehicle();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your vehivcle import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
