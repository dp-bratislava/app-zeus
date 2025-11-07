<?php

namespace App\Filament\Imports\Ticket;

use Dpb\Package\Tickets\Models\TicketGroup;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TicketGroupImporter extends Importer
{
    protected static ?string $model = TicketGroup::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code'),
            ImportColumn::make('title'),
            ImportColumn::make('parent')
                ->relationship('parent', 'code')
                // ->rules(['required', 'max:255']),
                ->rules(['max:255']),    
        ];
    }

    public function resolveRecord(): ?TicketGroup
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new TicketGroup();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your ticket group import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
