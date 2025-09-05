<?php

namespace App\Filament\Imports\Fleet;

use Dpb\Packages\Vehicles\Models\VehicleStatus;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class VehicleStatusImporter extends Importer
{
    protected static ?string $model = VehicleStatus::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code'),
            ImportColumn::make('title'),
            ImportColumn::make('description')
        ];
    }

    public function resolveRecord(): ?VehicleStatus
    {
        return VehicleStatus::firstOrNew(['code' => $this->data['code']], $this->data);

        // return new VehicleStatus();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your vehicle status import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
