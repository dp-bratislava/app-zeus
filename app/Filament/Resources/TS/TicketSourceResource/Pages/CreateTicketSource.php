<?php

namespace App\Filament\Resources\TS\TicketSourceResource\Pages;

use App\Filament\Resources\TS\TicketSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateTicketSource extends CreateRecord
{
    protected static string $resource = TicketSourceResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket-source.create_heading');
    }     
}
