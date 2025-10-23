<?php

namespace App\Filament\Resources\TS\TicketItemResource\Pages;

use App\Filament\Resources\TS\TicketItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicketItem extends CreateRecord
{
    protected static string $resource = TicketItemResource::class;
}
