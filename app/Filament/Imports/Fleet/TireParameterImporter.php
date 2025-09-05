<?php

namespace App\Filament\Imports\Fleet;

use Dpb\Packages\Vehicles\Models\Tire\Parameter;
use Dpb\Packages\Vehicles\Models\Vehicle;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class TireParameterImporter extends Importer
{
    protected static ?string $model = Parameter::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('tire_width'),
            ImportColumn::make('profile_number'),
            ImportColumn::make('construction_type')
            ->relationship('constructionType', 'code')
            // ->rules(['required', 'max:255']),
            ->rules(['max:255']),
            ImportColumn::make('rim_diameter'),
            ImportColumn::make('load_index'),
            ImportColumn::make('speed_rating'),
        ];
    }

    public function resolveRecord(): ?Parameter
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Parameter();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your tire parameter import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
