<?php

namespace App\Filament\Resources\TS\TicketGroupResource\Pages;

use App\Filament\Resources\TS\TicketGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListTicketGroups extends ListRecords
{
    protected static string $resource = TicketGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket-group.list_heading');
    }      
}
