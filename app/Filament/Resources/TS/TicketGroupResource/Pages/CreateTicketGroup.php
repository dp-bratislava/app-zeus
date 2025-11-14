<?php

namespace App\Filament\Resources\TS\TicketGroupResource\Pages;

use App\Filament\Resources\TS\TicketGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateTicketGroup extends CreateRecord
{
    protected static string $resource = TicketGroupResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket-group.create_heading');
    }      
}
