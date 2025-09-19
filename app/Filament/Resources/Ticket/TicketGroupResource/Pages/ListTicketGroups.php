<?php

namespace App\Filament\Resources\Ticket\TicketGroupResource\Pages;

use App\Filament\Resources\Ticket\TicketGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketGroups extends ListRecords
{
    protected static string $resource = TicketGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
