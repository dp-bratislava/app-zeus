<?php

namespace App\Filament\Resources\Ticket\TicketSourceResource\Pages;

use App\Filament\Resources\Ticket\TicketSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketSource extends EditRecord
{
    protected static string $resource = TicketSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
