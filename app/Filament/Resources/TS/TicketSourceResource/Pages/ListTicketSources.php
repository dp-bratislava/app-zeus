<?php

namespace App\Filament\Resources\TS\TicketSourceResource\Pages;

use App\Filament\Resources\TS\TicketSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListTicketSources extends ListRecords
{
    protected static string $resource = TicketSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket-source.list_heading');
    }     
}
