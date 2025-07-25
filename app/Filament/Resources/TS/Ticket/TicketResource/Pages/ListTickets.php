<?php

namespace App\Filament\Resources\TS\Ticket\TicketResource\Pages;

use App\Filament\Resources\TS\Ticket\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
