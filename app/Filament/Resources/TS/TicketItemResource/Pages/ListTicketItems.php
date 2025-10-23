<?php

namespace App\Filament\Resources\TS\TicketItemResource\Pages;

use App\Filament\Resources\TS\TicketItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketItems extends ListRecords
{
    protected static string $resource = TicketItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
