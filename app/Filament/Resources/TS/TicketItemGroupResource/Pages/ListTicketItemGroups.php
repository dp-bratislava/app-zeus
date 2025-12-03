<?php

namespace App\Filament\Resources\TS\TicketItemGroupResource\Pages;

use App\Filament\Resources\TS\TicketItemGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListTicketItemGroups extends ListRecords
{
    protected static string $resource = TicketItemGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    }      
}
