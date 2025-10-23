<?php

namespace App\Filament\Resources\TS\TicketSourceResource\Pages;

use App\Filament\Resources\TS\TicketSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketSources extends ListRecords
{
    protected static string $resource = TicketSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
