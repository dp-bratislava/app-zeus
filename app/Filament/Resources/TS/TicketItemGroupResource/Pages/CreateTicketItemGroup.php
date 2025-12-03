<?php

namespace App\Filament\Resources\TS\TicketItemGroupResource\Pages;

use App\Filament\Resources\TS\TicketItemGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateTicketItemGroup extends CreateRecord
{
    protected static string $resource = TicketItemGroupResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket-item-group.create_heading');
    }      
}
