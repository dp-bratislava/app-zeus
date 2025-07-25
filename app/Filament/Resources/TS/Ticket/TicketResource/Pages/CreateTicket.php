<?php

namespace App\Filament\Resources\TS\Ticket\TicketResource\Pages;

use App\Filament\Resources\TS\Ticket\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
}
